# 🧪 Auditoría Express Backend API — BAMBU v2 (Laravel 11 + PostgreSQL)

> Resultado: hallazgos + acciones inmediatas + plan de tests específico para lo ya implementado.

---

## 0) TL;DR (prioridades de esta semana)
- **Unificar estados de `pedidos`** entre BD ↔ docs/API (`en_reparto` vs `listo_envio/en_transito`).
- **Versionar autenticación** igual que el resto (`/api/v1/auth/login`) o documentar excepción.
- **Errores y trazabilidad**: respuesta de error única + `X-Request-ID` en request/response.
- **Idempotencia** para escrituras críticas (stock, pedidos).
- **Rate limit** por **usuario** y por **IP**; tokens con **expiración/rotación** en prod.
- **OpenAPI** como contrato y **contract testing** en CI (Schemathesis/Dredd).
- **SDK TS + mocks** para el FE (genera desde OpenAPI).

---

## 1) Hallazgos clave (gap analysis)

### 1.1 Estados de negocio
- **Docs/API**: `pedidos` usan `borrador → confirmado → listo_envio → en_transito → entregado | fallido/cancelado`.
- **BD**: `pedidos.estado` permite `borrador, confirmado, en_reparto, entregado, cancelado` (no están `listo_envio` ni `en_transito`).

**Riesgo:** incoherencia funcional, filtros y badge UI rotos, queries/reportes inconsistentes.

**Acción (elegí UNA estrategia):**
1) **Estandarizar en BD** a la cadena usada por la API/docs: `listo_envio` y `en_transito` (migración abajo).
2) **Estandarizar en API** a `en_reparto` (y ajustar mapeos FE/UX).

### 1.2 Autenticación sin versión
- **Auth** expuesta en `/api/login|register|logout` mientras el resto usa `/api/v1/...`  
**Acción:** mover a `/api/v1/auth/*` **o** documentar explícitamente que **Auth queda sin versión** (y por qué).

### 1.3 Seguridad y límites
- **Tokens** de desarrollo **sin expiración**; rate limit **sólo por IP**.  
**Acción:** en prod, **expiración/rotación**, refresh y límites por **usuario** además de IP.

### 1.4 Contratos/errores
- Endpoints y ejemplos detallados, pero **sin contrato OpenAPI fuente de verdad**.  
- Estructura de error varía (a veces `message`, a veces validación Laravel).  
**Acción:** contrato OpenAPI + **error envelope** uniforme:  
```json
{ "error": { "code":"...", "message":"...", "details":{}, "requestId":"..." } }
```

### 1.5 Idempotencia y concurrencia
- No se documenta `Idempotency-Key` para POST/PUT críticos (stock, pedidos).  
**Acción:** aceptar header y evitar efectos duplicados ante reintentos.

---

## 2) Acciones inmediatas (snippets)

### 2.1 Migración: estados `pedidos`
> Estandarizar a `borrador, confirmado, listo_envio, en_transito, entregado, cancelado`

```php
// database/migrations/2025_08_08_update_pedidos_estados.php
return new class extends Migration {
  public function up(): void {
    DB::statement("ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_estado_check");
    DB::statement("ALTER TABLE pedidos ADD CONSTRAINT pedidos_estado_check 
      CHECK (estado IN ('borrador','confirmado','listo_envio','en_transito','entregado','cancelado'))");
    // Opcional: migrar datos existentes
    DB::statement("UPDATE pedidos SET estado='en_transito' WHERE estado='en_reparto'");
  }
  public function down(): void {
    DB::statement("ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_estado_check");
    DB::statement("ALTER TABLE pedidos ADD CONSTRAINT pedidos_estado_check 
      CHECK (estado IN ('borrador','confirmado','en_reparto','entregado','cancelado'))");
    DB::statement("UPDATE pedidos SET estado='en_reparto' WHERE estado='en_transito'");
  }
};
```

### 2.2 Versionar Auth (opción A)
```php
// routes/api.php
Route::prefix('v1')->group(function () {
  Route::prefix('auth')->group(function() {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/register', [AuthController::class, 'register'])->middleware('can:admin');
  });
  // ... resto v1
});
```

### 2.3 Middleware de correlación + error envelope
```php
// app/Http/Middleware/CorrelationId.php
public function handle($request, Closure $next) {
  $rid = $request->header('X-Request-ID') ?? (string) Str::uuid();
  Log::withContext(['requestId' => $rid, 'userId' => optional(auth()->user())->id]);
  $response = $next($request);
  return $response->header('X-Request-ID', $rid);
}
```
```php
// app/Exceptions/Handler.php (render)
return response()->json([
  'error' => [
    'code' => $this->mapCode($e),
    'message' => $e->getMessage(),
    'details' => $this->details($e),
    'requestId' => request()->header('X-Request-ID')
  ]
], $this->status($e));
```

### 2.4 Idempotencia (stock/pedidos)
```php
// app/Http/Middleware/Idempotency.php
$key = $request->header('Idempotency-Key');
abort_if(!$key, 400, 'Idempotency-Key requerido');
$hash = 'idem:' . sha1($request->path().$request->method().$key.auth()->id());

if ($cached = Cache::get($hash)) {
  return response()->json($cached['body'], $cached['status']);
}

$response = $next($request);
Cache::put($hash, ['body' => $response->getOriginalContent(), 'status' => $response->status()], now()->addMinutes(10));
return $response;
```
Aplicar a: `POST /stock/ajustar`, `POST /stock/ingreso`, `POST /pedidos`, `PATCH /repartos/{id}/estado`.

### 2.5 Rate limit por usuario
```php
// RouteServiceProvider
RateLimiter::for('api-read', fn($r) => Limit::perMinute(60)->by(optional($r->user())->id ?: $r->ip()));
RateLimiter::for('api-write', fn($r) => Limit::perMinute(20)->by(optional($r->user())->id ?: $r->ip()));
```

---

## 3) Plan de tests específico (Pest + HTTP)

> Incluye casos reales de tu dominio (químicos/logística).

### 3.1 Stock — Ajustes y auditoría
- **422** si ajuste negativo sin `motivo`.
- **200/201** con auditoría completa (`stock_anterior/nuevo`, `usuario`, `lote_produccion`).
- **Idempotencia**: mismo `Idempotency-Key` → una sola auditoría.
- **Concurrencia**: 2 requests simultáneos no producen inconsistencias.

```php
it('rechaza ajuste negativo sin motivo', function () {
  $u = User::factory()->create();
  $p = Product::factory()->create(['stock_actual' => 10]);
  actingAs($u)->postJson('/api/v1/stock/ajustar', [
    'producto_id' => $p->id, 'nueva_cantidad' => 5 // negativo
  ])->assertStatus(422);
});

it('es idempotente en ajustes', function () {
  $u = User::factory()->create();
  $p = Product::factory()->create(['stock_actual' => 10]);
  $h = ['Idempotency-Key' => Str::uuid()];
  actingAs($u)->postJson('/api/v1/stock/ajustar', ['producto_id'=>$p->id,'nueva_cantidad'=>20,'motivo'=>'reconteo'],$h)->assertOk();
  actingAs($u)->postJson('/api/v1/stock/ajustar', ['producto_id'=>$p->id,'nueva_cantidad'=>20,'motivo'=>'reconteo'],$h)->assertOk();
  expect(StockMovimiento::where('producto_id',$p->id)->count())->toBe(1);
});
```

### 3.2 Pedidos — Estados y flujo
- **Transición válida**: `borrador→confirmado→listo_envio→en_transito→entregado`.
- **Bloquear** transiciones inválidas (`entregado→borrador`).
- **Migración de datos**: `en_reparto` migrado a `en_transito` (script de verificación).

```php
it('enforcea workflow de pedidos', function () {
  $pedido = Pedido::factory()->create(['estado'=>'borrador']);
  $pedido->confirmar();    // domain service / policy
  $pedido->marcarListoEnvio();
  $pedido->marcarEnTransito();
  $pedido->marcarEntregado();
  expect($pedido->estado)->toBe('entregado');
});
```

### 3.3 Repartos — Capacidad y estados
- **Capacidad**: suma de `peso_kg * cantidad` ≤ `vehiculo.capacidad_kg`.
- **Estados**: `programado→en_ruta→entregado|fallido`; sincronizar con estado del pedido (`en_transito/entregado`).

```php
it('no permite superar capacidad del vehículo', function () {
  $v = Vehiculo::factory()->create(['capacidad_kg'=>500]);
  $pedido = Pedido::factory()->conPeso(600)->create(['estado'=>'confirmado']);
  $reparto = RepartoService::programar($pedido, $v);
  expect($reparto)->toBeNull(); // o 422
});
```

### 3.4 Reportes/Dashboard
- **/reportes/dashboard** devuelve todas las métricas esperadas (ventas_totales, pedidos_mes/hoy, etc.).
- **Auto-refresh**: latencia < 200ms con datos seed (target).

```php
it('expone métricas de dashboard', function () {
  actingAs(User::factory()->create())
    ->getJson('/api/v1/reportes/dashboard')
    ->assertOk()
    ->assertJsonStructure([
      'ventas_totales','pedidos_mes','pedidos_hoy','clientes_activos','entregas_hoy',
      'vehiculos_disponibles','vehiculos_totales','pedidos_recientes','productos_destacados'
    ]);
});
```

### 3.5 Seguridad
- **Auth** obligatoria en rutas 🔒; públicas sólo las declaradas.
- **Rate limit** distinto para lectura/escritura.
- **Logs JSON + X-Request-ID** en cada request.

---

## 4) Contract testing (OpenAPI)

1) Crear `docs/api/openapi.yaml` con todos los endpoints actuales.
2) CI: correr **Schemathesis** (property-based) o **Dredd** (against running API).

```bash
# Schemathesis
st run docs/api/openapi.yaml --base-url http://localhost:8000 --checks all --workers 4
```

Generar **SDK TypeScript** y **mocks** para el FE:
```bash
npx @openapitools/openapi-generator-cli generate -i docs/api/openapi.yaml -g typescript-fetch -o frontend/sdk
npx @stoplight/prism-cli mock docs/api/openapi.yaml
```

---

## 5) Smoke tests (curl)

```bash
# login (dev)
curl -s -X POST http://localhost:8000/api/login -H "Content-Type: application/json" -d '{"email":"admin@bambu.com","password":"password"}'

# stock bajo (auth)
curl -s "http://localhost:8000/api/v1/stock?stock_bajo=true" -H "Authorization: Bearer $TOKEN"

# ajuste idempotente
curl -s -X POST "http://localhost:8000/api/v1/stock/ajustar" \
  -H "Authorization: Bearer $TOKEN" -H "Idempotency-Key: 123e4567-e89b-12d3-a456-426614174000" \
  -H "Content-Type: application/json" \
  -d '{"producto_id":1,"nueva_cantidad":50,"motivo":"reconteo"}'
```

---

## 6) Checklist “backend listo” (actualizado)

- [ ] Estados **unificados** en BD/API/UX
- [ ] **Auth versionada** o excepción documentada
- [ ] **Error envelope** + `X-Request-ID`
- [ ] **Idempotencia** en escrituras críticas
- [ ] **Rate limit** por usuario/IP + expiración tokens (prod)
- [ ] **OpenAPI** completa + SDK + mocks
- [ ] **Contract tests** en CI (verde)
- [ ] **E2E críticos** (pedido→reparto→entrega)
- [ ] **Logs JSON** con correlación y métricas p95/p99
- [ ] **Staging con seed** realista y datos auditables

---

## 7) Notas finales
- Tenés muy bien armado **stock/auditoría** y **reportes**. El mayor impacto inmediato es **coherencia de estados**, **contratos** y **operacionalidad** (trazabilidad, idempotencia, límites).
- Te dejo este archivo listo para PR en `docs/` y usarlo de guía de hardening.

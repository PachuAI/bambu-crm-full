# BAMBU — Hardening de API Backend (Laravel 11 + PostgreSQL + Sanctum)
**Objetivo:** dejar la API **predecible, resiliente y auditable**, lista para un frontend Vue 3 industrial (tablets, desktops) sin sorpresas.  
**Entregable:** documento ejecutable por tu agente con instrucciones paso a paso, explicaciones del **por qué**, **para qué**, **qué soluciona**, **errores que evita** y **qué pasa si no se aplica**.

> Contexto de dominio: distribución de **productos químicos** (ácidos, bases, neutros, especiales). Unidades: **bidón 5 L** como base (múltiplos→ 20 L = 4×5 L) y **IBC 1000 L**. Reglas de **peligrosidad (GHS/ADR)** e **incompatibilidades** afectan pedidos, stock, transporte y UI.

---

## 0) Alcance y metas
- **Contrato único** de API (OpenAPI) versionado (`/api/v1`) y **errores normalizados**.
- **Consistencia de estados** de negocio (pedidos, repartos, stock) a nivel **BD ↔ API ↔ UX**.
- **Escrituras seguras**: idempotencia, transacciones atómicas y control de concurrencia.
- **Seguridad y permisos**: auth con expiración/rotación, rate-limit por usuario/IP, CORS.
- **Observabilidad**: logs JSON con `X-Request-ID`, métricas p95/p99, trazabilidad y auditoría.
- **CI/CD** con tests unit/feature/contract/E2E + SDK TS generado y **mocks** para FE.

**Por qué:** el frontend queda **bloqueado** si la API cambia sin contrato o devuelve errores heterogéneos.  
**Qué evita:** regresiones silenciosas, datos inconsistentes, tickets por “pantallas rotas”.  
**Si no se aplica:** cada feature de FE demandará retrabajo y “hotfixes” inestables.

---

## 1) Contrato único (OpenAPI) y versionado

### 1.1 Acción
- Crear `docs/api/openapi.yaml` **fuente de verdad**.  
- Todas las rutas publicadas bajo **`/api/v1`** (incluida **auth** o documentar su excepción).  
- **Política de cambios:** breaking → `v2`; no-breaking → `minor/patch`.

```yaml
openapi: 3.0.3
info: { title: BAMBU API, version: "1.0.0" }
servers: [{ url: https://api.bambu.local/api/v1 }]
paths:
  /orders:
    post:
      summary: Crear pedido
      requestBody:
        required: true
        content:
          application/json:
            schema: { $ref: "#/components/schemas/CreateOrder" }
      responses:
        "201":
          description: creado
          content:
            application/json:
              schema: { $ref: "#/components/schemas/Order" }
components:
  schemas:
    Order:
      type: object
      required: [id, status, items]
      properties:
        id: { type: string, format: uuid }
        status: { $ref: "#/components/schemas/OrderStatus" }
        items: { type: array, items: { $ref: "#/components/schemas/OrderItem" } }
    OrderStatus:
      type: string
      enum: [borrador, confirmado, listo_envio, en_transito, entregado, cancelado]
    OrderItem:
      type: object
      required: [product_id, quantity, unit]
      properties:
        product_id: { type: string, format: uuid }
        quantity: { type: integer, minimum: 1 }
        unit: { type: string, enum: [BIDON_5L, IBC_1000L] }
    CreateOrder:
      type: object
      required: [customer_id, items]
      properties:
        customer_id: { type: string, format: uuid }
        items: { type: array, items: { $ref: "#/components/schemas/OrderItem" } }
```

### 1.2 Por qué / Para qué
- **Contrato estable** → frontend puede **generar SDK** y desarrollar sin ambigüedad.
- **Versionar** evita romper clientes existentes al crecer.

### 1.3 Qué soluciona / Errores evitados
- Cambios ad-hoc que rompen vistas.  
- Errores distintos por endpoint → manejo complejo en FE.

### 1.4 Si no se aplica
- **Retrabajo crónico** en FE, bugs difíciles de reproducir, hotfixes que degradan calidad.

---

## 2) Error envelope normalizado

### 2.1 Acción
Implementar **formato único** de error en `app/Exceptions/Handler.php` + **Correlation ID** middleware:

```json
{
  "error": {
    "code": "RESOURCE_NOT_FOUND",
    "message": "Producto no encontrado",
    "details": {"id":"..."},
    "requestId": "9e6c..."
  }
}
```

**Middleware `CorrelationId`:**
```php
public function handle($request, Closure $next) {
  $rid = $request->header('X-Request-ID') ?? (string) Str::uuid();
  Log::withContext(['requestId' => $rid, 'userId' => optional(auth()->user())->id]);
  return $next($request)->header('X-Request-ID', $rid);
}
```

**Handler (render):**
```php
return response()->json([
  'error' => [
    'code' => $this->mapCode($e),
    'message' => $this->mapMessage($e),
    'details' => $this->mapDetails($e),
    'requestId' => request()->header('X-Request-ID')
  ]
], $this->status($e));
```

### 2.2 Por qué / Para qué
- **DX**: FE maneja **un solo formato** de error.  
- **Operaciones**: `requestId` permite correlacionar logs en incidentes.

### 2.3 Qué soluciona / Errores evitados
- Errores de validación y de negocio heterogéneos.  
- Dificultad para reproducir e investigar problemas.

### 2.4 Si no se aplica
- Manejo de errores frágil en FE, tiempo perdido en debugging sin trazas.

---

## 3) Estados de negocio consistentes (BD ↔ API ↔ UX)

### 3.1 Acción
Homologar **`OrderStatus`** y aplicar **CHECK constraint** en PostgreSQL. Migración sugerida:

```php
// database/migrations/2025_08_08_update_orders_status_check.php
return new class extends Migration {
  public function up(): void {
    DB::statement("ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_estado_check");
    DB::statement("ALTER TABLE pedidos ADD CONSTRAINT pedidos_estado_check 
      CHECK (estado IN ('borrador','confirmado','listo_envio','en_transito','entregado','cancelado'))");
    DB::statement("UPDATE pedidos SET estado='en_transito' WHERE estado='en_reparto'");
  }
  public function down(): void {
    DB::statement("ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_estado_check");
    DB::statement("ALTER TABLE pedidos ADD CONSTRAINT pedidos_estado_check 
      CHECK (estado IN ('borrador','confirmado','en_reparto','entregado','cancelado'))");
  }
};
```

Definir **FSM** (Finite State Machine) de pedidos y **políticas** para transiciones.

### 3.2 Por qué / Para qué
- Los **badges** y filtros en UI dependen de estados **exactos**.  
- Reportes y analítica consistentes.

### 3.3 Qué soluciona / Errores evitados
- Inconsistencia entre nombres (`en_reparto` vs `en_transito`).  
- Datos imposibles (saltos de estado ilegales).

### 3.4 Si no se aplica
- Tableros inconsistentes, métricas incorrectas, bugs difíciles de trazar.

---

## 4) Idempotencia en escrituras críticas

### 4.1 Acción
Middleware `Idempotency` para `POST/PUT` críticos (pedidos, ajustes/ingresos de stock, cambios de estado):

```php
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

### 4.2 Por qué / Para qué
- **Reintentos** (red móvil/tablet en depósito) no deben **duplicar** movimientos/pedidos.

### 4.3 Qué soluciona / Errores evitados
- Pedidos duplicados, doble reserva, doble ajuste de stock por “doble tap”.

### 4.4 Si no se aplica
- **Incidencias contables** y de stock; reconciliación manual costosa.

---

## 5) Transacciones y concurrencia

### 5.1 Acción
Encapsular flujos críticos en **transacciones** y aplicar **bloqueos** adecuados.

```php
DB::transaction(function () use ($dto) {
  $order = Order::create($dto->order());
  $reservation = $this->stock->reserve($dto->items()); // valida lotes/vencimientos
  $order->attachReservation($reservation);
  // si algo falla → rollback automático
}, $attempts = 3);
```

Usar **bloqueo optimista** (columna `version`) o pesimista al ajustar stock crítico.

### 5.2 Por qué / Para qué
- Garantizar **todo-o-nada** en operaciones compuestas (pedido + reserva + saldo).

### 5.3 Qué soluciona / Errores evitados
- Estados intermedios corruptos (pedido creado sin reserva).

### 5.4 Si no se aplica
- **Datos huérfanos** y errores sutiles en reportes y UI (muestran pedidos “confirmados” sin stock).

---

## 6) Seguridad, permisos y límites

### 6.1 Acción
- **Auth**: Laravel Sanctum con expiración/rotación en prod (refresh tokens).  
- **RBAC/ABAC** con `Policies` por recurso (`order.create`, `stock.adjust`, etc.).  
- **Rate limit** lectura/escritura por **usuario** + fallback por IP.  
- **CORS**: orígenes del FE, headers `Authorization`, `Idempotency-Key`, `X-Request-ID`.  
- **Auditoría**: tabla `activity_logs` con actor, acción, entidad, antes/después.

### 6.2 Por qué / Para qué
- Minimizar impacto de credenciales expuestas y abuso de endpoints.

### 6.3 Qué soluciona / Errores evitados
- Tokens eternos que se filtran → acceso indefinido.  
- Flood de operaciones (ajustes, pedidos) por scripts.

### 6.4 Si no se aplica
- **Incidentes de seguridad**, costos por uso indebido y datos adulterados sin rastro.

---

## 7) Paginación, filtros, orden y búsqueda

### 7.1 Acción
- Formato único: `?page=1&per_page=25&sort=created_at:desc&filter[field]=value`  
- Respuesta paginada con `data` + `meta { page, per_page, total }`.  
- Búsqueda server-side indexada (ILIKE + trigram si aplica), **accent-insensitive**.

### 7.2 Por qué / Para qué
- Evitar duplicar lógica de listado en FE.  
- Consultas predecibles y **performantes**.

### 7.3 Qué soluciona / Errores evitados
- Respuestas heterogéneas que rompen componentes de tabla reutilizables.  
- Full scans innecesarios en tablas grandes.

### 7.4 Si no se aplica
- **Vistas lentas** en tablets/desktop y bugs en filtros/orden.

---

## 8) Observabilidad y auditoría

### 8.1 Acción
- **Logs JSON** con `requestId`, usuario, latencia, resultado.  
- **Métricas** p50/p95/p99 por endpoint; errores por tipo; colas (Horizon).  
- **Tracing**: eco de `X-Request-ID` en respuesta.  
- **Auditoría inmutable**: cambios de precios, stock, pedidos (quién, qué, antes→después).

### 8.2 Por qué / Para qué
- Detectar **degradaciones** y **explicar** cada cambio crítico (compliance y operativa).

### 8.3 Qué soluciona / Errores evitados
- “Nadie sabe por qué cambió el stock/precio”.  
- Dificultad para encontrar el origen de un bug de latencia.

### 8.4 Si no se aplica
- **Ceguera operativa**: incidentes largos, pérdida de confianza del negocio.

---

## 9) Datos, migraciones y seed

### 9.1 Acción
- Migraciones reproducibles; prohibidos cambios manuales.  
- **Factories/Seeders** para staging: 50 productos (variar peligrosidad), 200 clientes, picos estacionales.  
- Lotes con **vencimiento** realista y compatibilidades para probar bloqueos.

### 9.2 Por qué / Para qué
- Staging **creíble** permite detectar issues antes del release.

### 9.3 Qué soluciona / Errores evitados
- Bugs que sólo aparecen con datos reales (picos/agro, stock crítico).

### 9.4 Si no se aplica
- Releases rotos “solo en prod”, sin forma rápida de replicar.

---

## 10) Tests: unit, feature, contract y E2E

### 10.1 Acción
- **Unitarias**: reglas puras (reservas, compatibilidades, conversión de unidades).  
- **Feature (HTTP)**: auth/permiso/validaciones/errores normalizados.  
- **Contract**: OpenAPI ↔ implementación (Schemathesis/Dredd) en CI.  
- **E2E crítico**: pedido → reserva stock → confirmar → reparto → entrega.

**Pest ejemplos:** (resumen)
```php
it('es idempotente en ajustes de stock', function () { /* … */ });
it('enforcea workflow de pedidos', function () { /* … */ });
it('no permite superar capacidad de vehículo', function () { /* … */ });
it('expone métricas de dashboard', function () { /* … */ });
```
**Schemathesis:**
```bash
st run docs/api/openapi.yaml --base-url http://localhost:8000 --checks all --workers 4
```

### 10.2 Por qué / Para qué
- Romper rápido y barato en CI, no en producción.

### 10.3 Qué soluciona / Errores evitados
- Endpoints que se desvían del contrato, workflows inválidos.

### 10.4 Si no se aplica
- **Regresiones** frecuentes y costos de soporte altos.

---

## 11) Integración con FE: SDK + mocks

### 11.1 Acción
- Generar **SDK TypeScript** desde OpenAPI (typescript-fetch o axios).  
- Levantar **mocks** (Prism) para que el FE avance sin bloquearse.

```bash
npx @openapitools/openapi-generator-cli generate -i docs/api/openapi.yaml -g typescript-fetch -o frontend/sdk
npx @stoplight/prism-cli mock docs/api/openapi.yaml
```

### 11.2 Por qué / Para qué
- Aislar FE del ritmo del BE; **desacoplar** releases.

### 11.3 Qué soluciona / Errores evitados
- Bloqueo del FE esperando endpoints o datos precisos.

### 11.4 Si no se aplica
- Calendarios cruzados, tiempos muertos y presión sobre el equipo BE.

---

## 12) CI/CD mínimo viable

### 12.1 Acción
Pipeline (GitHub Actions / GitLab CI) con:
1) Lint + análisis estático (Pint, PHPStan).  
2) Tests unit/feature.  
3) **Contract tests**.  
4) Build **SDK** y publicar artefacto.  
5) Deploy a **staging** si todo está verde.

### 12.2 Por qué / Para qué
- Confianza de release; feedback temprano.

### 12.3 Qué soluciona / Errores evitados
- Subir a prod código no testeado o con contrato roto.

### 12.4 Si no se aplica
- Releases fallidos y urgencias de madrugada.

---

## 13) Performance y consultas

### 13.1 Acción
- Índices compuestos para filtros más usados (`estado`, `cliente_id`, `created_at`).  
- Evitar **N+1** con `->with()` en listados; **paginación** estricta.  
- Jobs/colas (Horizon) para tareas pesadas (reportes, cálculos masivos).  
- Cache selectiva de **lecturas** inmutables (catálogo).

### 13.2 Por qué / Para qué
- Tablets y redes inestables necesitan respuestas **rápidas y livianas**.

### 13.3 Qué soluciona / Errores evitados
- Latencias altas que rompen la experiencia en logística.

### 13.4 Si no se aplica
- Reclamos por lentitud, timeouts y “doble click” → inconsistencias.

---

## 14) Seguridad de datos y saneamiento

### 14.1 Acción
- Validación server-side exhaustiva (`FormRequest`).  
- Prevenir **asignación masiva** (`$fillable/$guarded`).  
- **Sanear** entradas de texto y **codificar** salidas.  
- Limitar **uploads** (MIME/size) y escanear si aplica.  
- **No** filtrar stack traces a cliente en prod.

### 14.2 Por qué / Para qué
- Reducir superficie de ataque y datos corruptos.

### 14.3 Qué soluciona / Errores evitados
- Inyecciones, XSS indirecto en backoffice, archivos peligrosos.

### 14.4 Si no se aplica
- Riesgos legales y técnicos (caída del servicio, fuga de datos).

---

## 15) Playbooks operativos (resumen)

- **Pedido duplicado** reportado: buscar por `X-Request-ID`, revisar cache de Idempotency, verificar auditoría.  
- **Stock negativo**: revisar transacciones recientes, lotes y bloqueos; check de reglas de compatibilidad.  
- **Lentitud**: inspeccionar métricas p95/p99 por endpoint, Horizon, logs de DB y N+1.

---

## 16) Checklist “Backend listo” (marcar todo como ✅)

- [ ] `docs/api/openapi.yaml` completo y publicado.  
- [ ] **/api/v1** consistente (auth incluida o excepción documentada).  
- [ ] **Error envelope** + `X-Request-ID` en toda la API.  
- [ ] Estados **homologados** (BD ↔ API) con **CHECK** y FSM.  
- [ ] **Idempotencia** en POST/PUT críticos.  
- [ ] **Transacciones** y bloqueos en flujos compuestos.  
- [ ] **Auth** con expiración/rotación + **rate limit** por usuario/IP.  
- [ ] **Paginación/filtros/orden** uniformes; búsqueda indexada.  
- [ ] **Logs JSON**, métricas y auditoría inmutable.  
- [ ] **Seed** realista en staging y migraciones reproducibles.  
- [ ] Tests **unit/feature/contract/E2E** verdes en CI.  
- [ ] **SDK TS** generado y **mocks** disponibles para el FE.  

---

## 17) Anexos — Snippets listos para copiar

### 17.1 Rate limiting por usuario/IP
```php
RateLimiter::for('api-read', fn($r) => Limit::perMinute(60)->by(optional($r->user())->id ?: $r->ip()));
RateLimiter::for('api-write', fn($r) => Limit::perMinute(20)->by(optional($r->user())->id ?: $r->ip()));
```

### 17.2 CORS (ejemplo)
```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['https://app.bambu.local'],
'allowed_headers' => ['Content-Type','Authorization','Idempotency-Key','X-Request-ID'],
'exposed_headers' => ['X-Request-ID'],
```

### 17.3 Tests Pest — workflow de pedidos (esqueleto)
```php
it('enforcea workflow de pedidos', function () {
  $pedido = Pedido::factory()->create(['estado'=>'borrador']);
  $pedido->confirmar();          // domain service o policy
  $pedido->marcarListoEnvio();
  $pedido->marcarEnTransito();
  $pedido->marcarEntregado();
  expect($pedido->estado)->toBe('entregado');
});
```

### 17.4 CI – contract testing
```yaml
- run: npx @stoplight/prism-cli mock docs/api/openapi.yaml &
- run: pip install schemathesis && st run docs/api/openapi.yaml --base-url http://localhost:8000 --checks all --workers 4
```

---

## 18) Guía para tu agente (orden de implementación)

1) Crear **openapi.yaml** (alcance actual) y versionar `/api/v1`.  
2) Agregar **CorrelationId** + error envelope.  
3) Homologar **estados** en BD con CHECK y migración de valores.  
4) Implementar **Idempotency** middleware en endpoints críticos.  
5) Blindar flujos con **DB::transaction** + bloqueos.  
6) Endurecer **auth** (expiración/rotación), **rate limit**, **CORS**.  
7) Unificar **paginación/filtros/orden** y búsqueda indexada.  
8) Añadir **logs JSON**, métricas y auditoría.  
9) Generar **SDK TS** y **mocks**; integrar **contract tests** en CI.  
10) Poblar **staging** con seed realista y ejecutar suite completa.

> Con esto, el frontend puede avanzar **sin fricción** y el sistema queda preparado para crecer con **bajo costo de mantenimiento**.

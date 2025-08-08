# 📦 Backend API Readiness — Sistema Bambú (Laravel 11 + PostgreSQL)

> Objetivo: dejar la **API lista para consumo** por el frontend (Vue 3), con contratos estables, calidad y observabilidad. Checklist accionable + ejemplos y tooling.

---

## 1) Alcance y entregables

**Alcance:** API pública del CRM (productos, stock, pedidos, clientes, precios, reservas), seguridad y observabilidad.  
**Entregables:**
- `docs/api/openapi.yaml` (contrato fuente de verdad)
- **SDK TS** para el frontend (`frontend/sdk/`)
- **Mocks** de la API desde OpenAPI
- **Tests** (unit + feature + contract) verdes en CI
- **Ambiente staging** con seed realista y métricas

---

## 2) Contrato y errores normalizados

### 2.1 OpenAPI (fuente única de verdad)
- Archivo: `docs/api/openapi.yaml`
- Debe cubrir **todos** los endpoints expuestos al frontend
- Versionado (ej. `v1`) y **breaking changes** → bump de versión

**Ejemplo mínima estructura:**
```yaml
openapi: 3.0.3
info: { title: Bambu API, version: "1.0.0" }
servers: [{ url: https://api.bambu.local/v1 }]
paths:
  /products:
    get:
      summary: List products
      parameters:
        - in: query
          name: q
          schema: { type: string }
        - in: query
          name: page
          schema: { type: integer, minimum: 1, default: 1 }
      responses:
        "200":
          description: ok
          content:
            application/json:
              schema:
                $ref: "#/components/schemas/PaginatedProducts"
components:
  schemas:
    PaginatedProducts:
      type: object
      properties:
        data: { type: array, items: { $ref: "#/components/schemas/Product" } }
        meta: { $ref: "#/components/schemas/Pagination" }
    Product:
      type: object
      required: [id, name, hazard_class]
      properties:
        id: { type: string, format: uuid }
        name: { type: string }
        hazard_class: { type: string, enum: [none, corrosive, flammable, toxic, reactive] }
        unit: { type: string, enum: [BIDON_5L, IBC_1000L] }
    Pagination:
      type: object
      properties:
        page: { type: integer }
        per_page: { type: integer }
        total: { type: integer }
```

### 2.2 Modelo de error único
Respuesta de error **en todos los endpoints**:
```json
{
  "error": {
    "code": "RESOURCE_NOT_FOUND",
    "message": "Producto no encontrado",
    "details": { "id": "…" },
    "requestId": "7a2b…"
  }
}
```
- `code` estable y documentado
- `requestId` (correlation id) **obligatorio**

---

## 3) Seguridad y permisos

- **Auth**: JWT o Sanctum (bearer). Expiración + refresh.
- **RBAC/ABAC** por recurso. Ej.: `order.create`, `order.cancel`, `stock.move`.
- **Rate limit** por actor/endpoint (Throttle). Ej.: 60 req/min para lectura, 20 req/min para escritura.
- **CORS**: dominios del frontend + `Authorization`, `Idempotency-Key`, `X-Request-ID` en headers permitidos.
- **Auditoría** (tabla `activity_logs`): quién hizo qué y cuándo, con payload mínimo.

**Laravel (ejemplos):**
```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('api-read', fn(Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
RateLimiter::for('api-write', fn(Request $r) => Limit::perMinute(20)->by($r->user()?->id ?: $r->ip()));
```

---

## 4) Especificidad de dominio (químicos/logística)

- **Stock en tiempo real** con **reservas** (no solo decremento al facturar)
- **Lotes y vencimientos** en movimientos de stock
- **Unidades**: base = **bidón 5L**; múltiplos (p.ej. 4×5L=20L), **IBC 1000L**
- **Peligrosidad** (GHS/ADR) en producto y reglas de **incompatibilidad** para transporte/almacenaje
- **Bloqueos de pedido** por seguridad (estado “blocked_safety”)
- **Listas de precios** por cliente y condiciones de pago
- **Eventos**/webhooks (p. ej. `order.created`, `stock.reserved`)

**Endoints mínimos:**
- `/products`, `/batches`, `/stock-movements`, `/stock-reservations`
- `/orders` (crear/cancelar/confirmar/pick/dispatch), `/customers`, `/price-lists`

---

## 5) Escrituras seguras: idempotencia, transacciones, concurrencia

- **Idempotencia** para POST/PUT con header `Idempotency-Key`
- **Transacciones** DB para flujos atómicos (pedido ↔ reserva ↔ saldo)
- **Bloqueo** pesimista/optimista en stock crítico

**Laravel (esqueleto):**
```php
DB::transaction(function () use ($dto) {
  $order = Order::create($dto->order());
  $reservation = Stock::reserve($dto->items()); // valida lotes/vencimientos
  $order->attachReservation($reservation);
});
```

---

## 6) Paginación/filtrado/búsqueda (consistencia)

- **Formato único**: `?page=1&per_page=25&sort=created_at:desc&filter[field]=value`
- Todas las listas con **meta**: `{ page, per_page, total }`
- **Búsqueda** server-side con índices adecuados (ILIKE trigram si aplica)

---

## 7) Observabilidad

- **Logs JSON** (Monolog) con `requestId`, usuario y latencia
- **Métricas**: p50/p95/p99 por endpoint; errores por tipo; colas (jobs en Horizon)
- **Tracing** (si aplica): header `X-Request-ID` eco en respuesta
- **Alertas**: tasas de error y latencia fuera de umbrales

**Middleware de correlación (ejemplo):**
```php
$requestId = $request->header('X-Request-ID') ?? Str::uuid();
Log::withContext(['requestId' => $requestId]);
return $next($request)->header('X-Request-ID', $requestId);
```

---

## 8) Datos, migraciones y seed

- **Migraciones reproducibles**; nada de cambios manuales en prod
- **Seed realista** para staging: ~50 productos (variar peligrosidad), 200 clientes, estacionalidad de pedidos
- **Factories** para generar lotes con fechas de vencimiento

```bash
php artisan migrate:fresh --seed --env=staging
```

---

## 9) Tests (unit, feature, contract, E2E)

- **Unitarias**: reglas de negocio puras (reservas, compatibilidades, conversiones de unidades)
- **Feature (HTTP)**: endpoints con auth, permisos, validaciones y errores
- **Contract**: validar OpenAPI ↔ implementación (Schemathesis/Dredd)
- **E2E crítico**: crear pedido → reservar stock → confirmar → movimientos

**Pest (ejemplo feature):**
```php
it('crea pedido y reserva stock', function () {
  $u = User::factory()->withRole('seller')->create();
  $p = Product::factory()->hazard('corrosive')->create();
  $this->actingAs($u)->postJson('/api/v1/orders', [
    'items' => [['product_id' => $p->id, 'quantity' => 4, 'unit' => 'BIDON_5L']],
  ])->assertCreated()->assertJsonPath('status', 'reserved');
});
```

**Contract testing (CLI):**
```bash
# Schemathesis (desde openapi.yaml)
st run --checks all --workers 4 http://localhost:8000/docs/api/openapi.yaml --base-url http://localhost:8000
```

---

## 10) SDK y mocks para el frontend

- Generar **SDK TypeScript** desde OpenAPI:
```bash
npx @openapitools/openapi-generator-cli generate \
  -i docs/api/openapi.yaml \
  -g typescript-fetch \
  -o frontend/sdk
```
- **Mocks** desde OpenAPI (p. ej. Prism):
```bash
npx @stoplight/prism-cli mock docs/api/openapi.yaml
```
Frontend desarrolla contra mocks sin bloquearse.

---

## 11) CI/CD (pipeline sugerido)

**Jobs:**
1. **Lint & Static Analysis** (PHPStan, Pint)
2. **Tests** (unit/feature) + cobertura mínima
3. **Contract tests** (Schemathesis/Dredd)
4. **Build SDK** y publicar artefacto
5. **Deploy** a staging tras verde

**GitHub Actions (boceto):**
```yaml
name: api-ci
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_USER: laravel
          POSTGRES_PASSWORD: secret
          POSTGRES_DB: bambu_test
        ports: ["5432:5432"]
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: "8.3" }
      - run: cp .env.example .env && composer install && php artisan key:generate
      - run: php artisan migrate --env=testing
      - run: ./vendor/bin/pest
      - run: ./vendor/bin/phpstan analyse
      - run: npx @stoplight/prism-cli mock docs/api/openapi.yaml &
      - run: pip install schemathesis && st run docs/api/openapi.yaml --base-url http://localhost:8000
```

---

## 12) Criterios de aceptación (“backend listo”)

- ✅ `docs/api/openapi.yaml` completo y versionado
- ✅ Respuestas de error normalizadas (con `requestId`)
- ✅ Auth + permisos por recurso + rate limiting en marcha
- ✅ Transacciones/idempotencia en escrituras críticas
- ✅ Paginación/filtros/orden **consistentes** en todas las listas
- ✅ Logs JSON + métricas p95/p99 + tracing básico
- ✅ Staging con seed realista y backups
- ✅ Tests verdes (unit/feature/contract/E2E)
- ✅ SDK TS generado y mocks funcionando

---

## 13) Qué archivos quiero revisar (pasame esto)

- `routes/api.php`, `app/Http/Controllers/**`, `app/Policies/**`
- `app/Http/Requests/**` (FormRequest de validaciones)
- `app/Models/**` (métodos de dominio: reservas, lotes, etc.)
- `database/migrations/**`, `database/seeders/**`, `database/factories/**`
- `config/auth.php`, `app/Providers/RouteServiceProvider.php`
- Middleware de correlación/logs (si existe)
- `docs/api/openapi.yaml` (si ya lo tienen)
- Cualquier `Job`/`Listener` ligado a stock/pedidos

Con eso hago una **auditoría express** + propongo **tests específicos** para tu caso.

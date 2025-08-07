# Guía de Desarrollo y Mejores Prácticas - Sistema BAMBU

## 1. PRINCIPIOS FUNDAMENTALES

### 1.1 Filosofía de Desarrollo
- **Keep It Simple, Stupid (KISS)**: Soluciones simples antes que complejas
- **Don't Repeat Yourself (DRY)**: Reutilización de código
- **You Aren't Gonna Need It (YAGNI)**: No sobre-ingenierizar
- **Boy Scout Rule**: Dejar el código mejor de como lo encontraste
- **Fail Fast**: Detectar errores temprano

### 1.2 Estándares de Código
```yaml
Backend (PHP/Laravel):
  - PSR-12: Coding Standard
  - Laravel Best Practices
  - Type Declarations obligatorios
  - Strict Types habilitado

Frontend (TypeScript/Vue):
  - ESLint + Prettier
  - Vue Style Guide oficial
  - Composition API
  - Script Setup syntax
```

## 2. ESTRUCTURA DEL PROYECTO

### 2.1 Organización por Dominios
```
src/
├── Domain/
│   ├── Catalog/           # Productos y categorías
│   ├── Sales/            # Ventas y cotizaciones
│   ├── Logistics/        # Logística y entregas
│   └── Shared/           # Compartido entre dominios
```

### 2.2 Estructura de un Módulo
```
Domain/Sales/
├── Models/               # Entidades del dominio
├── ValueObjects/         # Objetos de valor
├── Services/            # Lógica de negocio
├── Repositories/        # Interfaces de persistencia
├── Events/              # Eventos del dominio
├── Exceptions/          # Excepciones específicas
└── Tests/               # Tests del módulo
```

## 3. CONVENCIONES DE CÓDIGO

### 3.1 Nomenclatura

#### PHP/Laravel
```php
// Clases: PascalCase
class ProductController {}

// Métodos: camelCase
public function createProduct() {}

// Variables: camelCase
$productName = 'Limpiador';

// Constantes: UPPER_SNAKE_CASE
const MAX_DISCOUNT_PERCENTAGE = 15;

// Tablas DB: snake_case plural
Schema::create('product_categories', ...);

// Columnas DB: snake_case
$table->string('product_name');
```

#### TypeScript/Vue
```typescript
// Componentes: PascalCase
export default defineComponent({
  name: 'ProductCard'
});

// Composables: use prefix
export function useProductSearch() {}

// Tipos/Interfaces: PascalCase
interface ProductData {
  id: string;
  name: string;
}

// Enums: PascalCase
enum OrderStatus {
  Pending = 'pending',
  Confirmed = 'confirmed'
}
```

### 3.2 Estructura de Archivos

#### Controladores
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\CreateQuotationRequest;
use App\Http\Resources\Sales\QuotationResource;
use App\Domain\Sales\Services\QuotationService;
use Illuminate\Http\JsonResponse;

final class QuotationController extends Controller
{
    public function __construct(
        private readonly QuotationService $quotationService
    ) {}

    public function store(CreateQuotationRequest $request): JsonResponse
    {
        $quotation = $this->quotationService->create(
            $request->toCommand()
        );

        return QuotationResource::make($quotation)
            ->response()
            ->setStatusCode(201);
    }
}
```

#### Servicios
```php
<?php

declare(strict_types=1);

namespace App\Domain\Sales\Services;

use App\Domain\Sales\Models\Quotation;
use App\Domain\Sales\Commands\CreateQuotationCommand;
use App\Domain\Sales\Events\QuotationCreated;
use Illuminate\Support\Facades\DB;

final class QuotationService
{
    public function create(CreateQuotationCommand $command): Quotation
    {
        return DB::transaction(function () use ($command) {
            $quotation = Quotation::create([
                'customer_id' => $command->customerId,
                'items' => $command->items,
                'notes' => $command->notes,
            ]);

            event(new QuotationCreated($quotation));

            return $quotation;
        });
    }
}
```

## 4. TESTING

### 4.1 Estructura de Tests
```
tests/
├── Unit/           # Tests unitarios rápidos
├── Feature/        # Tests de integración
├── Browser/        # Tests E2E con Dusk
└── TestCase.php    # Clase base
```

### 4.2 Convenciones de Testing
```php
// Nombre descriptivo: test_it_<acción>_<contexto>
public function test_it_creates_quotation_with_valid_data(): void
{
    // Arrange
    $customer = Customer::factory()->create();
    $products = Product::factory()->count(3)->create();
    
    // Act
    $response = $this->postJson('/api/quotations', [
        'customer_id' => $customer->id,
        'items' => $products->map(fn($p) => [
            'product_id' => $p->id,
            'quantity' => 5
        ])
    ]);
    
    // Assert
    $response->assertCreated();
    $this->assertDatabaseHas('quotations', [
        'customer_id' => $customer->id
    ]);
}
```

### 4.3 Coverage Mínimo
- Unit Tests: 80%
- Feature Tests: 70%
- Crítico (pagos, stock): 95%

## 5. BASE DE DATOS

### 5.1 Migraciones
```php
// Siempre usar tipos específicos y constraints
Schema::create('products', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('sku', 50)->unique();
    $table->string('name', 200);
    $table->decimal('price', 10, 2);
    $table->integer('stock')->unsigned()->default(0);
    $table->boolean('is_active')->default(true);
    
    // Índices para queries frecuentes
    $table->index(['name', 'is_active']);
    $table->index('created_at');
    
    // Soft deletes y timestamps
    $table->softDeletes();
    $table->timestamps();
});
```

### 5.2 Modelos
```php
// Usar casts y atributos tipados
class Product extends Model
{
    use HasUuid, SoftDeletes;
    
    protected $fillable = [
        'sku',
        'name',
        'price',
        'stock',
        'is_active',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];
    
    // Accesors con tipo de retorno
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => '$' . number_format($this->price, 2)
        );
    }
}
```

## 6. API DESIGN

### 6.1 RESTful Endpoints
```yaml
# Convención de rutas
GET    /api/v1/products          # Listar
GET    /api/v1/products/{id}     # Detalle
POST   /api/v1/products          # Crear
PUT    /api/v1/products/{id}     # Actualizar completo
PATCH  /api/v1/products/{id}     # Actualizar parcial
DELETE /api/v1/products/{id}     # Eliminar

# Acciones custom
POST   /api/v1/products/{id}/activate
POST   /api/v1/quotations/{id}/confirm
```

### 6.2 Responses
```php
// Usar API Resources
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'price_formatted' => $this->price_formatted,
            'stock' => $this->stock,
            'is_available' => $this->stock > 0,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}

// Respuestas consistentes
return response()->json([
    'data' => $data,
    'meta' => [
        'total' => $total,
        'per_page' => $perPage,
    ],
    'links' => [
        'self' => $request->url(),
        'next' => $nextUrl,
    ]
]);
```

## 7. FRONTEND BEST PRACTICES

### 7.1 Componentes Vue
```vue
<!-- ProductCard.vue -->
<script setup lang="ts">
import { computed, defineProps, defineEmits } from 'vue'
import type { Product } from '@/types'

// Props tipadas
interface Props {
  product: Product
  showStock?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showStock: true
})

// Emits tipados
const emit = defineEmits<{
  select: [product: Product]
  delete: [id: string]
}>()

// Computed con tipo de retorno
const isAvailable = computed<boolean>(() => {
  return props.product.stock > 0
})
</script>

<template>
  <article 
    class="product-card"
    :class="{ 'product-card--unavailable': !isAvailable }"
  >
    <h3>{{ product.name }}</h3>
    <p>{{ product.priceFormatted }}</p>
    
    <p v-if="showStock">
      Stock: {{ product.stock }}
    </p>
    
    <button 
      @click="emit('select', product)"
      :disabled="!isAvailable"
    >
      Seleccionar
    </button>
  </article>
</template>

<style scoped>
.product-card {
  @apply p-4 border rounded-lg;
}

.product-card--unavailable {
  @apply opacity-50;
}
</style>
```

### 7.2 Composables
```typescript
// composables/useProductSearch.ts
import { ref, computed, watchEffect } from 'vue'
import { useDebounce } from '@vueuse/core'
import { productApi } from '@/api'
import type { Product } from '@/types'

export function useProductSearch() {
  const searchTerm = ref('')
  const products = ref<Product[]>([])
  const isLoading = ref(false)
  const error = ref<Error | null>(null)
  
  const debouncedSearch = useDebounce(searchTerm, 300)
  
  const hasResults = computed(() => products.value.length > 0)
  
  watchEffect(async () => {
    if (!debouncedSearch.value) {
      products.value = []
      return
    }
    
    isLoading.value = true
    error.value = null
    
    try {
      const response = await productApi.search({
        q: debouncedSearch.value
      })
      products.value = response.data
    } catch (e) {
      error.value = e as Error
      products.value = []
    } finally {
      isLoading.value = false
    }
  })
  
  return {
    searchTerm,
    products,
    isLoading,
    error,
    hasResults
  }
}
```

## 8. SEGURIDAD

### 8.1 Validación de Inputs
```php
// Siempre validar en el servidor
class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:50', 'unique:products'],
            'name' => ['required', 'string', 'max:200'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sku' => strtoupper(trim($this->sku)),
            'name' => trim($this->name),
        ]);
    }
}
```

### 8.2 Autorización
```php
// Usar Policies
class ProductPolicy
{
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('products.create');
    }
    
    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('products.update') 
            && $product->created_by === $user->id;
    }
}

// En el controlador
public function update(UpdateProductRequest $request, Product $product)
{
    $this->authorize('update', $product);
    // ...
}
```

## 9. PERFORMANCE

### 9.1 Optimización de Queries
```php
// Evitar N+1 queries
$orders = Order::with(['customer', 'items.product'])
    ->whereDate('created_at', today())
    ->get();

// Usar chunk para grandes datasets
Product::where('is_active', true)
    ->chunk(1000, function ($products) {
        foreach ($products as $product) {
            // Procesar
        }
    });

// Índices compuestos para queries complejas
$table->index(['status', 'created_at']);
```

### 9.2 Caching
```php
// Cache con tags
$products = Cache::tags(['products'])->remember(
    'products.active',
    3600,
    fn() => Product::active()->get()
);

// Invalidar cache
Cache::tags(['products'])->flush();
```

## 10. DOCUMENTACIÓN

### 10.1 Código Auto-documentado
```php
/**
 * Calcula el precio final con descuento aplicado
 * 
 * @param float $basePrice Precio base del producto
 * @param int $quantity Cantidad de productos
 * @param float $discountPercentage Porcentaje de descuento (0-100)
 * 
 * @return float Precio final con descuento
 * 
 * @throws InvalidArgumentException Si el descuento es mayor a 100
 */
public function calculateFinalPrice(
    float $basePrice, 
    int $quantity, 
    float $discountPercentage
): float {
    if ($discountPercentage > 100) {
        throw new InvalidArgumentException('Discount cannot exceed 100%');
    }
    
    $subtotal = $basePrice * $quantity;
    $discount = $subtotal * ($discountPercentage / 100);
    
    return round($subtotal - $discount, 2);
}
```

### 10.2 README por Módulo
```markdown
# Módulo de Ventas

## Descripción
Gestiona cotizaciones, pedidos y facturación.

## Estructura
- `Models/`: Entidades del dominio
- `Services/`: Lógica de negocio
- `Events/`: Eventos del dominio

## Uso
```php
$quotationService = app(QuotationService::class);
$quotation = $quotationService->create($command);
```

## Tests
```bash
php artisan test --filter=Sales
```
```

## 11. GIT WORKFLOW

### 11.1 Conventional Commits
```bash
# Formato: <tipo>(<alcance>): <descripción>

feat(sales): add discount calculation to quotations
fix(products): correct stock validation on update
docs(api): update endpoint documentation
refactor(logistics): extract route optimization logic
test(orders): add integration tests for order creation
chore(deps): update Laravel to 11.x
```

### 11.2 Branch Strategy
```bash
main              # Producción
├── develop       # Desarrollo
    ├── feature/  # Nuevas funcionalidades
    ├── fix/      # Correcciones
    └── hotfix/   # Fixes urgentes
```

## 12. CHECKLIST DE DESARROLLO

### Antes de hacer commit:
- [ ] Tests pasan (`php artisan test`)
- [ ] Código formateado (`./vendor/bin/pint`)
- [ ] Sin warnings de TypeScript
- [ ] Sin console.log() o dd()
- [ ] Documentación actualizada
- [ ] Migrations reversibles
- [ ] Performance aceptable

### Antes de PR:
- [ ] Todos los tests pasan
- [ ] Code coverage > 80%
- [ ] Sin código comentado
- [ ] Sin TODOs pendientes
- [ ] Documentación de API actualizada
- [ ] Changelog actualizado

---

**Documento creado**: Enero 2025  
**Versión**: 1.0  
**Mantenedor**: Equipo BAMBU  
**Actualización**: Mensual
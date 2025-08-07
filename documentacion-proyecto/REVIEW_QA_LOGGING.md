# Review de QA y Logging - Sistema BAMBU

## 1. ESTRATEGIA DE QUALITY ASSURANCE

### 1.1 Estado Actual del MVP
```yaml
Testing:
  - Tests automatizados: 0%
  - Tests manuales: Ad-hoc sin documentación
  - Cobertura de código: No medida
  - Bugs conocidos: No rastreados

Calidad:
  - Code review: No implementado
  - Estándares: No definidos
  - Linting: No configurado
  - CI/CD: No existe
```

### 1.2 Propuesta de QA Completa

#### Niveles de Testing
```
┌─────────────────────────────────────┐
│         E2E Tests (10%)             │  ← Flujos críticos completos
├─────────────────────────────────────┤
│     Integration Tests (30%)         │  ← API y componentes
├─────────────────────────────────────┤
│       Unit Tests (60%)              │  ← Lógica de negocio
└─────────────────────────────────────┘
```

#### Herramientas Recomendadas
```yaml
Backend:
  - PHPUnit: Unit tests base
  - Pest: Feature tests más legibles
  - Laravel Dusk: Browser automation
  - PHPStan: Static analysis (nivel 8)
  - Laravel Pint: Code formatting

Frontend:
  - Vitest: Unit tests Vue components
  - Cypress: E2E testing
  - ESLint + Prettier: Code quality
  - TypeScript: Type safety
  - Playwright: Cross-browser testing
```

### 1.3 Testing Strategy por Módulo

#### Módulo Crítico: Cotizador
```php
// tests/Feature/QuotationTest.php
class QuotationTest extends TestCase
{
    /** @test */
    public function it_calculates_discount_correctly_for_each_level()
    {
        // Arrange
        $products = Product::factory()->count(3)->create([
            'precio_base_l1' => 100
        ]);
        
        // Test cada nivel de descuento
        $testCases = [
            ['total' => 500, 'expected_discount' => 0],    // L1
            ['total' => 5000, 'expected_discount' => 5],   // L2
            ['total' => 10000, 'expected_discount' => 10], // L3
            ['total' => 20000, 'expected_discount' => 15], // L4
        ];
        
        foreach ($testCases as $case) {
            // Act & Assert
            $this->assertDiscountCalculation($case['total'], $case['expected_discount']);
        }
    }
    
    /** @test */
    public function it_excludes_combo_products_from_discount_calculation()
    {
        // Test específico para regla de negocio de combos
    }
    
    /** @test */
    public function it_validates_stock_before_confirming_quotation()
    {
        // Test de validación de stock
    }
}
```

#### Tests de Integración API
```php
// tests/Integration/Api/ProductSearchTest.php
class ProductSearchTest extends TestCase
{
    /** @test */
    public function it_returns_products_matching_search_term()
    {
        // Given
        Product::factory()->create(['nombre' => 'Limpiador Multiuso']);
        Product::factory()->create(['nombre' => 'Desengrasante']);
        
        // When
        $response = $this->getJson('/api/search/productos?q=limpiador');
        
        // Then
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nombre', 'Limpiador Multiuso');
    }
}
```

### 1.4 Code Coverage Requirements

```yaml
Mínimos Requeridos:
  - Overall: 80%
  - Modelos: 90%
  - Servicios: 85%
  - Controllers: 75%
  - Helpers: 100%

Críticos (100% coverage):
  - Cálculo de precios
  - Descuentos
  - Control de stock
  - Validaciones de negocio
```

## 2. ESTRATEGIA DE LOGGING

### 2.1 Niveles de Log

```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack', 'bugsnag'],
        'ignore_exceptions' => false,
    ],
    
    'business' => [
        'driver' => 'daily',
        'path' => storage_path('logs/business.log'),
        'level' => 'info',
        'days' => 30,
    ],
    
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'warning',
        'days' => 90,
    ],
    
    'performance' => [
        'driver' => 'daily',
        'path' => storage_path('logs/performance.log'),
        'level' => 'info',
        'days' => 7,
    ],
]
```

### 2.2 Qué Loggear

#### Eventos de Negocio
```php
// app/Services/QuotationService.php
public function confirmQuotation(Quotation $quotation): Order
{
    Log::channel('business')->info('Quotation confirmed', [
        'quotation_id' => $quotation->id,
        'customer_id' => $quotation->customer_id,
        'total_amount' => $quotation->total,
        'discount_applied' => $quotation->discount_percentage,
        'user_id' => auth()->id(),
        'ip' => request()->ip(),
    ]);
    
    try {
        $order = DB::transaction(function () use ($quotation) {
            // Lógica de conversión
        });
        
        Log::channel('business')->info('Order created from quotation', [
            'order_id' => $order->id,
            'quotation_id' => $quotation->id,
        ]);
        
        return $order;
        
    } catch (\Exception $e) {
        Log::channel('business')->error('Failed to confirm quotation', [
            'quotation_id' => $quotation->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        throw $e;
    }
}
```

#### Eventos de Seguridad
```php
// app/Http/Middleware/LogSecurityEvents.php
public function handle($request, Closure $next)
{
    // Log intentos de acceso a rutas protegidas
    if ($request->is('admin/*')) {
        Log::channel('security')->info('Admin access attempt', [
            'user_id' => auth()->id(),
            'route' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
    
    $response = $next($request);
    
    // Log respuestas no autorizadas
    if ($response->status() === 403) {
        Log::channel('security')->warning('Unauthorized access attempt', [
            'user_id' => auth()->id(),
            'route' => $request->path(),
            'method' => $request->method(),
        ]);
    }
    
    return $response;
}
```

#### Performance Monitoring
```php
// app/Http/Middleware/PerformanceLogger.php
public function handle($request, Closure $next)
{
    $startTime = microtime(true);
    $startMemory = memory_get_usage();
    
    $response = $next($request);
    
    $duration = microtime(true) - $startTime;
    $memory = memory_get_usage() - $startMemory;
    
    if ($duration > 1.0) { // Log slow requests
        Log::channel('performance')->warning('Slow request detected', [
            'route' => $request->path(),
            'method' => $request->method(),
            'duration' => round($duration, 3),
            'memory_mb' => round($memory / 1048576, 2),
            'user_id' => auth()->id(),
        ]);
    }
    
    return $response;
}
```

### 2.3 Structured Logging

```php
// app/Logging/StructuredLogger.php
class StructuredLogger
{
    public static function logBusinessEvent(string $event, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'session_id' => session()->getId(),
            'request_id' => request()->header('X-Request-ID'),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ];
        
        Log::channel('business')->info($event, array_merge($baseContext, $context));
    }
}

// Uso
StructuredLogger::logBusinessEvent('product.stock.updated', [
    'product_id' => $product->id,
    'old_stock' => $oldStock,
    'new_stock' => $newStock,
    'reason' => 'manual_adjustment',
]);
```

## 3. MONITORING Y ALERTAS

### 3.1 Métricas Clave

```yaml
Business Metrics:
  - Cotizaciones por hora
  - Tasa de conversión cotización->pedido
  - Valor promedio de pedido
  - Productos sin stock
  - Tiempo promedio de entrega

Technical Metrics:
  - Response time (p50, p95, p99)
  - Error rate por endpoint
  - Queue jobs fallidos
  - Uso de CPU/memoria
  - Queries lentas
```

### 3.2 Alertas Configuradas

```php
// app/Monitors/BusinessMonitor.php
class BusinessMonitor
{
    public function checkStockLevels(): void
    {
        $lowStockProducts = Product::where('stock_actual', '<', 'stock_minimo')->count();
        
        if ($lowStockProducts > 5) {
            Log::channel('alerts')->critical('Multiple products with low stock', [
                'count' => $lowStockProducts,
                'products' => Product::lowStock()->pluck('nombre'),
            ]);
            
            // Enviar notificación
            Notification::send(
                User::role('admin')->get(),
                new LowStockAlert($lowStockProducts)
            );
        }
    }
    
    public function checkOrderProcessing(): void
    {
        $pendingOrders = Order::pending()
            ->where('created_at', '<', now()->subHours(2))
            ->count();
            
        if ($pendingOrders > 0) {
            Log::channel('alerts')->warning('Orders pending processing', [
                'count' => $pendingOrders,
            ]);
        }
    }
}
```

## 4. DEBUGGING TOOLS

### 4.1 Laravel Telescope (Development)
```php
// config/telescope.php
'watchers' => [
    Watchers\RequestWatcher::class => [
        'enabled' => true,
        'size_limit' => 100,
    ],
    Watchers\QueryWatcher::class => [
        'enabled' => true,
        'slow' => 100, // ms
    ],
    Watchers\LogWatcher::class => true,
    Watchers\ExceptionWatcher::class => true,
    Watchers\JobWatcher::class => true,
    Watchers\ModelWatcher::class => true,
    Watchers\CacheWatcher::class => true,
    Watchers\RedisWatcher::class => true,
];
```

### 4.2 Query Debugging
```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    if (config('app.debug')) {
        DB::listen(function ($query) {
            if ($query->time > 100) {
                Log::channel('slow-queries')->warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            }
        });
    }
}
```

## 5. ERROR HANDLING

### 5.1 Global Exception Handler
```php
// app/Exceptions/Handler.php
public function report(Throwable $exception)
{
    if ($this->shouldReport($exception)) {
        // Log context adicional
        Log::error($exception->getMessage(), [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'url' => request()->fullUrl(),
            'input' => request()->except(['password', 'password_confirmation']),
            'user' => auth()->user()?->only(['id', 'email']),
        ]);
    }
    
    parent::report($exception);
}
```

### 5.2 Business Exception Handling
```php
// app/Exceptions/InsufficientStockException.php
class InsufficientStockException extends Exception
{
    public function __construct(
        public Product $product,
        public int $requested,
        public int $available
    ) {
        parent::__construct(
            "Stock insuficiente para {$product->nombre}. Solicitado: {$requested}, Disponible: {$available}"
        );
        
        Log::channel('business')->warning('Stock validation failed', [
            'product_id' => $product->id,
            'product_name' => $product->nombre,
            'requested_quantity' => $requested,
            'available_quantity' => $available,
        ]);
    }
}
```

## 6. AUDIT TRAIL

### 6.1 Database Auditing
```php
// app/Traits/Auditable.php
trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            AuditLog::create([
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'event' => 'created',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'data' => $model->toArray(),
            ]);
        });
        
        static::updated(function ($model) {
            AuditLog::create([
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'event' => 'updated',
                'user_id' => auth()->id(),
                'old_data' => $model->getOriginal(),
                'new_data' => $model->getChanges(),
            ]);
        });
    }
}
```

## 7. CHECKLIST DE QA

### Pre-Deployment
- [ ] Todos los tests pasan
- [ ] Code coverage > 80%
- [ ] No hay console.log() o dd()
- [ ] Logs configurados correctamente
- [ ] Alertas configuradas
- [ ] Performance tests ejecutados
- [ ] Security scan ejecutado

### Post-Deployment
- [ ] Logs fluyendo correctamente
- [ ] Métricas siendo recolectadas
- [ ] Alertas funcionando
- [ ] Backups verificados
- [ ] Rollback plan probado

---

**Documento creado**: Enero 2025  
**Versión**: 1.0  
**QA Lead**: Sistema BAMBU Team  
**Próxima revisión**: Mensual
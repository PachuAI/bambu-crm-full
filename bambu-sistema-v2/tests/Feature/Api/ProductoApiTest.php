<?php

namespace Tests\Feature\Api;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductoApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'ProductosSeeder']);
    }

    public function test_puede_listar_productos_sin_autenticacion(): void
    {
        $response = $this->getJson('/api/v1/productos');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'current_page',
                        'data' => [
                            '*' => [
                                'id',
                                'nombre',
                                'sku',
                                'precio_base_l1',
                                'stock_actual',
                                'es_combo',
                                'marca_producto',
                                'descripcion',
                                'peso_kg',
                                'created_at',
                                'updated_at',
                            ]
                        ],
                        'total',
                        'per_page'
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertGreaterThan(0, $response->json('data.total'));
    }

    public function test_puede_ver_producto_especifico(): void
    {
        $producto = Producto::first();

        $response = $this->getJson("/api/v1/productos/{$producto->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'nombre',
                        'sku',
                        'precio_base_l1',
                        'stock_actual',
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($producto->id, $response->json('data.id'));
    }

    public function test_devuelve_404_para_producto_inexistente(): void
    {
        $response = $this->getJson('/api/v1/productos/99999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ]);
    }

    public function test_puede_buscar_productos_por_termino(): void
    {
        $response = $this->getJson('/api/v1/productos/buscar/BAMBU');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'nombre',
                            'sku',
                            'precio_base_l1',
                        ]
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        
        // Verificar que todos los resultados contienen 'BAMBU'
        $productos = $response->json('data');
        foreach ($productos as $producto) {
            $this->assertTrue(
                stripos($producto['nombre'], 'BAMBU') !== false ||
                stripos($producto['sku'], 'BAMBU') !== false ||
                stripos($producto['marca_producto'], 'BAMBU') !== false
            );
        }
    }

    public function test_puede_filtrar_productos_por_marca(): void
    {
        $response = $this->getJson('/api/v1/productos?marca=BAMBU');

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $productos = $response->json('data.data');
        foreach ($productos as $producto) {
            $this->assertEquals('BAMBU', $producto['marca_producto']);
        }
    }

    public function test_puede_filtrar_productos_en_stock(): void
    {
        $response = $this->getJson('/api/v1/productos?en_stock=true');

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $productos = $response->json('data.data');
        foreach ($productos as $producto) {
            $this->assertGreaterThan(0, $producto['stock_actual']);
        }
    }

    public function test_puede_filtrar_combos(): void
    {
        $response = $this->getJson('/api/v1/productos?es_combo=true');

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $productos = $response->json('data.data');
        foreach ($productos as $producto) {
            $this->assertTrue($producto['es_combo']);
        }
    }

    public function test_puede_buscar_productos_con_search(): void
    {
        $response = $this->getJson('/api/v1/productos?search=Shampoo');

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        // Si encuentra resultados, verificar que contienen 'Shampoo'
        $productos = $response->json('data.data');
        if (count($productos) > 0) {
            foreach ($productos as $producto) {
                $this->assertTrue(
                    stripos($producto['nombre'], 'Shampoo') !== false ||
                    stripos($producto['descripcion'], 'Shampoo') !== false
                );
            }
        }
    }

    public function test_requiere_autenticacion_para_crear_producto(): void
    {
        $productData = [
            'nombre' => 'Nuevo Producto Test',
            'sku' => 'TEST-NEW-001',
            'precio_base_l1' => 1500.00,
            'stock_actual' => 20,
            'marca_producto' => 'BAMBU',
        ];

        $response = $this->postJson('/api/v1/productos', $productData);

        $response->assertStatus(401);
    }

    public function test_puede_crear_producto_autenticado(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $productData = [
            'nombre' => 'Nuevo Producto Test',
            'sku' => 'TEST-NEW-001',
            'precio_base_l1' => 1500.00,
            'stock_actual' => 20,
            'marca_producto' => 'BAMBU',
        ];

        $response = $this->postJson('/api/v1/productos', $productData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'nombre',
                        'sku',
                        'precio_base_l1',
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('Producto creado exitosamente', $response->json('message'));

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Nuevo Producto Test',
            'sku' => 'TEST-NEW-001',
            'precio_base_l1' => 1500.00,
        ]);
    }

    public function test_valida_campos_requeridos_al_crear(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/productos', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre', 'sku', 'precio_base_l1']);
    }

    public function test_valida_sku_unico_al_crear(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $producto = Producto::first();

        $productData = [
            'nombre' => 'Producto Duplicado',
            'sku' => $producto->sku, // SKU existente
            'precio_base_l1' => 1500.00,
        ];

        $response = $this->postJson('/api/v1/productos', $productData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['sku']);
    }

    public function test_puede_actualizar_producto_autenticado(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $producto = Producto::first();

        $updateData = [
            'nombre' => 'Producto Actualizado',
            'sku' => 'UPDATED-001',
            'precio_base_l1' => 2000.00,
            'stock_actual' => 50,
        ];

        $response = $this->putJson("/api/v1/productos/{$producto->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Producto actualizado exitosamente'
                ]);

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Producto Actualizado',
            'sku' => 'UPDATED-001',
        ]);
    }

    public function test_puede_eliminar_producto_autenticado(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $producto = Producto::create([
            'nombre' => 'Producto a Eliminar',
            'sku' => 'DELETE-001',
            'precio_base_l1' => 1000.00,
            'stock_actual' => 10,
        ]);

        $response = $this->deleteJson("/api/v1/productos/{$producto->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Producto eliminado exitosamente'
                ]);

        // Verificar soft delete
        $this->assertSoftDeleted('productos', ['id' => $producto->id]);
    }

    public function test_pagination_funciona_correctamente(): void
    {
        $response = $this->getJson('/api/v1/productos?per_page=5');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertArrayHasKey('current_page', $data);
        $this->assertArrayHasKey('per_page', $data);
        $this->assertArrayHasKey('total', $data);
        $this->assertLessThanOrEqual(5, count($data['data']));
    }
}

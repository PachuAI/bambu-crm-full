<?php

namespace Tests\Feature\Models;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductoModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'ProvinciasSeeder']);
        $this->artisan('db:seed', ['--class' => 'CiudadesSeeder']);
        $this->artisan('db:seed', ['--class' => 'NivelesDescuentoSeeder']);
    }

    public function test_puede_crear_producto_basico(): void
    {
        $producto = Producto::create([
            'nombre' => 'Test Producto',
            'sku' => 'TEST-001',
            'precio_base_l1' => 1000.50,
            'stock_actual' => 10,
            'es_combo' => false,
            'marca_producto' => 'BAMBU',
        ]);

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Test Producto',
            'sku' => 'TEST-001',
            'precio_base_l1' => 1000.50,
        ]);

        $this->assertEquals('Test Producto', $producto->nombre);
        $this->assertEquals(1000.50, (float) $producto->precio_base_l1);
        $this->assertFalse($producto->es_combo);
    }

    public function test_producto_requiere_campos_obligatorios(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Producto::create([
            // Falta nombre, sku, precio_base_l1
            'stock_actual' => 10,
        ]);
    }

    public function test_sku_debe_ser_unico(): void
    {
        Producto::create([
            'nombre' => 'Producto 1',
            'sku' => 'UNIQUE-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 10,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Producto::create([
            'nombre' => 'Producto 2',
            'sku' => 'UNIQUE-001', // SKU duplicado
            'precio_base_l1' => 2000,
            'stock_actual' => 5,
        ]);
    }

    public function test_casts_funcionan_correctamente(): void
    {
        $producto = Producto::create([
            'nombre' => 'Test Casts',
            'sku' => 'CAST-001',
            'precio_base_l1' => '1500.75',
            'stock_actual' => '25',
            'es_combo' => '1',
            'peso_kg' => '2.500',
        ]);

        $this->assertIsNumeric($producto->precio_base_l1);
        $this->assertIsInt($producto->stock_actual);
        $this->assertIsBool($producto->es_combo);
        $this->assertIsNumeric($producto->peso_kg);

        $this->assertEquals(1500.75, $producto->precio_base_l1);
        $this->assertEquals(25, $producto->stock_actual);
        $this->assertTrue($producto->es_combo);
        $this->assertEquals(2.500, $producto->peso_kg);
    }

    public function test_scopes_funcionan_correctamente(): void
    {
        // Crear productos de prueba
        $productoSinStock = Producto::create([
            'nombre' => 'Sin Stock',
            'sku' => 'SIN-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 0,
        ]);

        $productoConStock = Producto::create([
            'nombre' => 'Con Stock',
            'sku' => 'CON-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 10,
        ]);

        $combo = Producto::create([
            'nombre' => 'Combo Test',
            'sku' => 'COMBO-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 5,
            'es_combo' => true,
        ]);

        $productoBambu = Producto::create([
            'nombre' => 'Producto BAMBU',
            'sku' => 'BAMBU-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 5,
            'marca_producto' => 'BAMBU',
        ]);

        // Test scope enStock
        $productosEnStock = Producto::enStock()->get();
        $this->assertCount(3, $productosEnStock);
        $this->assertFalse($productosEnStock->contains($productoSinStock));

        // Test scope combo
        $combos = Producto::combo()->get();
        $this->assertCount(1, $combos);
        $this->assertTrue($combos->contains($combo));

        // Test scope porMarca
        $productosBambu = Producto::porMarca('BAMBU')->get();
        $this->assertCount(1, $productosBambu);
        $this->assertTrue($productosBambu->contains($productoBambu));
    }

    public function test_accessor_peso_total(): void
    {
        $producto = Producto::create([
            'nombre' => 'Test Peso',
            'sku' => 'PESO-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 10,
            'peso_kg' => 2.5,
        ]);

        $this->assertEquals(25.0, $producto->peso_total); // 10 * 2.5
    }

    public function test_relacion_pedido_items(): void
    {
        $producto = Producto::create([
            'nombre' => 'Test Relación',
            'sku' => 'REL-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 10,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $producto->pedidoItems());
        $this->assertCount(0, $producto->pedidoItems);
    }

    public function test_relacion_movimientos_stock(): void
    {
        $producto = Producto::create([
            'nombre' => 'Test Movimientos',
            'sku' => 'MOV-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 10,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $producto->stockMovimientos());
        $this->assertCount(0, $producto->stockMovimientos);
    }

    public function test_soft_deletes_funcionan(): void
    {
        $producto = Producto::create([
            'nombre' => 'Test Delete',
            'sku' => 'DEL-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 10,
        ]);

        $productoId = $producto->id;

        // Soft delete
        $producto->delete();

        // No debe aparecer en consultas normales
        $this->assertCount(0, Producto::where('id', $productoId)->get());

        // Debe aparecer con withTrashed
        $this->assertCount(1, Producto::withTrashed()->where('id', $productoId)->get());

        // Debe estar marcado como eliminado
        $productoEliminado = Producto::withTrashed()->find($productoId);
        $this->assertNotNull($productoEliminado->deleted_at);
    }

    public function test_producto_puede_ser_restaurado(): void
    {
        $producto = Producto::create([
            'nombre' => 'Test Restore',
            'sku' => 'REST-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 10,
        ]);

        $productoId = $producto->id;

        // Eliminar y restaurar
        $producto->delete();
        $producto->restore();

        // Debe aparecer en consultas normales después del restore
        $this->assertCount(1, Producto::where('id', $productoId)->get());

        $productoRestaurado = Producto::find($productoId);
        $this->assertNull($productoRestaurado->deleted_at);
    }

    public function test_busqueda_por_terminos(): void
    {
        Producto::create([
            'nombre' => 'Shampoo Natural',
            'sku' => 'SHAM-001',
            'precio_base_l1' => 1000,
            'stock_actual' => 10,
            'descripcion' => 'Shampoo con ingredientes naturales',
        ]);

        Producto::create([
            'nombre' => 'Crema Hidratante',
            'sku' => 'CREAM-001',
            'precio_base_l1' => 1500,
            'stock_actual' => 5,
            'descripcion' => 'Crema para hidratar la piel',
        ]);

        // Buscar por nombre
        $resultados = Producto::where('nombre', 'LIKE', '%Shampoo%')->get();
        $this->assertCount(1, $resultados);

        // Buscar por SKU
        $resultados = Producto::where('sku', 'LIKE', '%SHAM%')->get();
        $this->assertCount(1, $resultados);

        // Buscar por descripción
        $resultados = Producto::where('descripcion', 'LIKE', '%naturales%')->get();
        $this->assertCount(1, $resultados);
    }
}

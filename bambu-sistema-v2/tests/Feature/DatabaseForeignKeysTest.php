<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseForeignKeysTest extends TestCase
{
    use RefreshDatabase;

    public function test_clientes_ciudad_foreign_key(): void
    {
        // Crear provincia
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Buenos Aires',
            'codigo' => 'BA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear ciudad
        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'La Plata',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear cliente con ciudad válida
        $cliente = DB::table('clientes')->insertGetId([
            'nombre' => 'Test Cliente',
            'direccion' => 'Test 123',
            'telefono' => '123456789',
            'email' => 'test@test.com',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente,
            'ciudad_id' => $ciudad,
        ]);

        // Intentar crear cliente con ciudad inexistente debe fallar
        $this->expectException(\Exception::class);
        DB::table('clientes')->insert([
            'nombre' => 'Test Cliente Inválido',
            'direccion' => 'Test 456',
            'telefono' => '987654321',
            'ciudad_id' => 99999, // ID inexistente
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_pedidos_cliente_foreign_key(): void
    {
        // Crear cliente
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Córdoba',
            'codigo' => 'CB',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'Córdoba Capital',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cliente = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Test',
            'direccion' => 'Dir 123',
            'telefono' => '123',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear pedido válido
        $pedido = DB::table('pedidos')->insertGetId([
            'cliente_id' => $cliente,
            'monto_bruto' => 1000.00,
            'monto_final' => 900.00,
            'estado' => 'borrador',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido,
            'cliente_id' => $cliente,
        ]);

        // Intentar crear pedido con cliente inexistente debe fallar
        $this->expectException(\Exception::class);
        DB::table('pedidos')->insert([
            'cliente_id' => 99999, // ID inexistente
            'monto_bruto' => 500.00,
            'monto_final' => 500.00,
            'estado' => 'borrador',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_pedido_items_foreign_keys(): void
    {
        // Preparar datos
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Mendoza',
            'codigo' => 'MZ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'Mendoza Capital',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cliente = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente FK Test',
            'direccion' => 'Dir FK',
            'telefono' => '456',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pedido = DB::table('pedidos')->insertGetId([
            'cliente_id' => $cliente,
            'monto_bruto' => 2000.00,
            'monto_final' => 1800.00,
            'estado' => 'confirmado',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $producto = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Test FK',
            'sku' => 'TEST-FK-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 50,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear pedido_item válido
        $item = DB::table('pedido_items')->insertGetId([
            'pedido_id' => $pedido,
            'producto_id' => $producto,
            'cantidad' => 5,
            'precio_unit_l1' => 90.00,
            'subtotal' => 450.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('pedido_items', [
            'id' => $item,
            'pedido_id' => $pedido,
            'producto_id' => $producto,
        ]);
    }

    public function test_unique_constraints(): void
    {
        // Test SKU único en productos
        DB::table('productos')->insert([
            'nombre' => 'Producto 1',
            'sku' => 'UNIQUE-TEST-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 10,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Intentar insertar otro producto con el mismo SKU debe fallar
        $this->expectException(\Exception::class);
        DB::table('productos')->insert([
            'nombre' => 'Producto 2',
            'sku' => 'UNIQUE-TEST-001', // SKU duplicado
            'precio_base_l1' => 200.00,
            'stock_actual' => 20,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_enum_constraints(): void
    {
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Santa Fe',
            'codigo' => 'SF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'Santa Fe Capital',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cliente = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Enum Test',
            'direccion' => 'Dir Enum',
            'telefono' => '789',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Estados válidos del enum
        $estadosValidos = ['borrador', 'confirmado', 'en_reparto', 'entregado', 'cancelado'];

        foreach ($estadosValidos as $estado) {
            $pedido = DB::table('pedidos')->insertGetId([
                'cliente_id' => $cliente,
                'monto_bruto' => 100.00,
                'monto_final' => 100.00,
                'estado' => $estado,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->assertDatabaseHas('pedidos', [
                'id' => $pedido,
                'estado' => $estado,
            ]);
        }

        // Estado inválido debe fallar
        $this->expectException(\InvalidArgumentException::class);
        \App\Models\Pedido::create([
            'cliente_id' => $cliente,
            'monto_bruto' => 100.00,
            'monto_final' => 100.00,
            'estado' => 'estado_invalido', // No está en el enum
        ]);
    }
}

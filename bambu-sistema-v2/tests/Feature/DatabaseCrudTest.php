<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_productos_crud_operations(): void
    {
        // CREATE - Insertar producto
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Test CRUD',
            'sku' => 'TEST-CRUD-001',
            'precio_base_l1' => 150.50,
            'stock_actual' => 25,
            'es_combo' => false,
            'marca_producto' => 'BAMBU',
            'descripcion' => 'Descripción del producto test',
            'peso_kg' => 2.750,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // READ - Verificar inserción
        $producto = DB::table('productos')->where('id', $productoId)->first();
        $this->assertNotNull($producto);
        $this->assertEquals('Producto Test CRUD', $producto->nombre);
        $this->assertEquals('TEST-CRUD-001', $producto->sku);
        $this->assertEquals('150.50', $producto->precio_base_l1);
        $this->assertEquals(25, $producto->stock_actual);
        $this->assertFalse((bool)$producto->es_combo);
        $this->assertEquals('BAMBU', $producto->marca_producto);

        // UPDATE - Actualizar producto
        DB::table('productos')
            ->where('id', $productoId)
            ->update([
                'precio_base_l1' => 175.00,
                'stock_actual' => 30,
                'updated_at' => now()
            ]);

        $productoActualizado = DB::table('productos')->where('id', $productoId)->first();
        $this->assertEquals('175.00', $productoActualizado->precio_base_l1);
        $this->assertEquals(30, $productoActualizado->stock_actual);

        // DELETE - Soft delete
        DB::table('productos')
            ->where('id', $productoId)
            ->update(['deleted_at' => now()]);

        $productoEliminado = DB::table('productos')->where('id', $productoId)->whereNull('deleted_at')->first();
        $this->assertNull($productoEliminado);

        $productoConSoftDelete = DB::table('productos')->where('id', $productoId)->first();
        $this->assertNotNull($productoConSoftDelete->deleted_at);
    }

    public function test_clientes_crud_operations(): void
    {
        // Preparar datos relacionados
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Buenos Aires',
            'codigo' => 'BA',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'La Plata',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // CREATE - Insertar cliente
        $clienteId = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Test CRUD',
            'direccion' => 'Calle Test 123',
            'telefono' => '221-123-4567',
            'email' => 'cliente@test.com',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // READ - Verificar inserción con JOIN
        $cliente = DB::table('clientes')
            ->join('ciudades', 'clientes.ciudad_id', '=', 'ciudades.id')
            ->join('provincias', 'ciudades.provincia_id', '=', 'provincias.id')
            ->where('clientes.id', $clienteId)
            ->select('clientes.*', 'ciudades.nombre as ciudad_nombre', 'provincias.nombre as provincia_nombre')
            ->first();

        $this->assertNotNull($cliente);
        $this->assertEquals('Cliente Test CRUD', $cliente->nombre);
        $this->assertEquals('cliente@test.com', $cliente->email);
        $this->assertEquals('La Plata', $cliente->ciudad_nombre);
        $this->assertEquals('Buenos Aires', $cliente->provincia_nombre);

        // UPDATE - Actualizar datos
        DB::table('clientes')
            ->where('id', $clienteId)
            ->update([
                'email' => 'nuevo@email.com',
                'telefono' => '221-999-8888',
                'updated_at' => now()
            ]);

        $clienteActualizado = DB::table('clientes')->where('id', $clienteId)->first();
        $this->assertEquals('nuevo@email.com', $clienteActualizado->email);
        $this->assertEquals('221-999-8888', $clienteActualizado->telefono);
    }

    public function test_pedido_complete_workflow(): void
    {
        // Preparar datos
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Córdoba',
            'codigo' => 'CB',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'Córdoba Capital',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $cliente = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Pedido Test',
            'direccion' => 'Av. Colón 123',
            'telefono' => '351-123-4567',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto1 = DB::table('productos')->insertGetId([
            'nombre' => 'Producto A',
            'sku' => 'PROD-A-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 50,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto2 = DB::table('productos')->insertGetId([
            'nombre' => 'Producto B',
            'sku' => 'PROD-B-001',
            'precio_base_l1' => 200.00,
            'stock_actual' => 30,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Crear pedido
        $pedido = DB::table('pedidos')->insertGetId([
            'cliente_id' => $cliente,
            'monto_bruto' => 700.00,
            'monto_final' => 630.00, // 10% descuento
            'estado' => 'borrador',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Agregar items al pedido
        DB::table('pedido_items')->insert([
            [
                'pedido_id' => $pedido,
                'producto_id' => $producto1,
                'cantidad' => 3,
                'precio_unit_l1' => 100.00,
                'subtotal' => 300.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'pedido_id' => $pedido,
                'producto_id' => $producto2,
                'cantidad' => 2,
                'precio_unit_l1' => 200.00,
                'subtotal' => 400.00,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Verificar pedido completo con JOINs
        $pedidoCompleto = DB::table('pedidos')
            ->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
            ->where('pedidos.id', $pedido)
            ->select('pedidos.*', 'clientes.nombre as cliente_nombre')
            ->first();

        $this->assertEquals('Cliente Pedido Test', $pedidoCompleto->cliente_nombre);
        $this->assertEquals('700.00', $pedidoCompleto->monto_bruto);
        $this->assertEquals('630.00', $pedidoCompleto->monto_final);

        // Verificar items del pedido
        $items = DB::table('pedido_items')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->where('pedido_items.pedido_id', $pedido)
            ->select('pedido_items.*', 'productos.nombre as producto_nombre')
            ->get();

        $this->assertCount(2, $items);
        
        $totalCalculado = $items->sum('subtotal');
        $this->assertEquals(700.00, $totalCalculado);

        // Actualizar estado del pedido
        DB::table('pedidos')
            ->where('id', $pedido)
            ->update([
                'estado' => 'confirmado',
                'fecha_reparto' => now()->addDays(3)->toDateString(),
                'updated_at' => now()
            ]);

        $pedidoConfirmado = DB::table('pedidos')->where('id', $pedido)->first();
        $this->assertEquals('confirmado', $pedidoConfirmado->estado);
        $this->assertNotNull($pedidoConfirmado->fecha_reparto);
    }

    public function test_system_logs_insertion(): void
    {
        // Insertar log del sistema
        $logId = DB::table('system_logs')->insertGetId([
            'modelo' => 'productos',
            'modelo_id' => 1,
            'accion' => 'CREATE',
            'datos_anteriores' => null,
            'datos_nuevos' => json_encode([
                'nombre' => 'Producto Nuevo',
                'sku' => 'NEW-001',
                'precio_base_l1' => 100.00
            ]),
            'user_id' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'TestAgent/1.0',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Verificar log
        $log = DB::table('system_logs')->where('id', $logId)->first();
        $this->assertNotNull($log);
        $this->assertEquals('productos', $log->modelo);
        $this->assertEquals('CREATE', $log->accion);
        
        $datosNuevos = json_decode($log->datos_nuevos, true);
        $this->assertEquals('Producto Nuevo', $datosNuevos['nombre']);
        $this->assertEquals('NEW-001', $datosNuevos['sku']);
    }

    public function test_bulk_operations(): void
    {
        // Inserción masiva de productos
        $productos = [];
        for ($i = 1; $i <= 10; $i++) {
            $productos[] = [
                'nombre' => "Producto Bulk {$i}",
                'sku' => "BULK-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'precio_base_l1' => 100.00 * $i,
                'stock_actual' => 10 * $i,
                'es_combo' => $i % 2 === 0,
                'marca_producto' => $i <= 5 ? 'BAMBU' : 'SAPHIRUS',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('productos')->insert($productos);

        // Verificar cantidad insertada
        $count = DB::table('productos')->count();
        $this->assertEquals(10, $count);

        // Consultas con filtros
        $bambuCount = DB::table('productos')->where('marca_producto', 'BAMBU')->count();
        $this->assertEquals(5, $bambuCount);

        $combosCount = DB::table('productos')->where('es_combo', true)->count();
        $this->assertEquals(5, $combosCount);

        // Actualización masiva
        DB::table('productos')
            ->where('marca_producto', 'BAMBU')
            ->update(['precio_base_l1' => DB::raw('precio_base_l1 * 1.1')]);

        $preciosActualizados = DB::table('productos')
            ->where('marca_producto', 'BAMBU')
            ->get(['precio_base_l1']);

        // Verificar que se aplicó el aumento del 10%
        $productos = DB::table('productos')
            ->where('marca_producto', 'BAMBU')
            ->orderBy('id')
            ->get(['id', 'precio_base_l1']);
        
        foreach ($productos as $index => $producto) {
            $precioEsperado = round(100.00 * ($index + 1) * 1.1, 2);
            $this->assertEquals($precioEsperado, round((float)$producto->precio_base_l1, 2));
        }
    }
}
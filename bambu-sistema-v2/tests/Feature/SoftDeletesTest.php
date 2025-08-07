<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SoftDeletesTest extends TestCase
{
    use RefreshDatabase;

    public function test_productos_soft_delete(): void
    {
        // Crear producto
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Soft Delete',
            'sku' => 'SOFT-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 10,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Verificar que existe
        $producto = DB::table('productos')->where('id', $productoId)->whereNull('deleted_at')->first();
        $this->assertNotNull($producto);

        // Soft delete
        DB::table('productos')
            ->where('id', $productoId)
            ->update(['deleted_at' => now()]);

        // Verificar que no aparece en consultas normales
        $productoEliminado = DB::table('productos')->where('id', $productoId)->whereNull('deleted_at')->first();
        $this->assertNull($productoEliminado);

        // Verificar que todavía existe con deleted_at
        $productoConSoftDelete = DB::table('productos')->where('id', $productoId)->first();
        $this->assertNotNull($productoConSoftDelete);
        $this->assertNotNull($productoConSoftDelete->deleted_at);

        // Verificar restore (quitar soft delete)
        DB::table('productos')
            ->where('id', $productoId)
            ->update(['deleted_at' => null]);

        $productoRestaurado = DB::table('productos')->where('id', $productoId)->whereNull('deleted_at')->first();
        $this->assertNotNull($productoRestaurado);
        $this->assertNull($productoRestaurado->deleted_at);
    }

    public function test_clientes_soft_delete(): void
    {
        // Preparar datos
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

        // Crear cliente
        $clienteId = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Soft Delete',
            'direccion' => 'Dir 123',
            'telefono' => '123456789',
            'email' => 'cliente@test.com',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Crear pedido para el cliente
        $pedidoId = DB::table('pedidos')->insertGetId([
            'cliente_id' => $clienteId,
            'monto_bruto' => 500.00,
            'monto_final' => 450.00,
            'estado' => 'confirmado',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Soft delete del cliente
        DB::table('clientes')
            ->where('id', $clienteId)
            ->update(['deleted_at' => now()]);

        // Verificar que el cliente no aparece en consultas normales
        $clienteEliminado = DB::table('clientes')->where('id', $clienteId)->whereNull('deleted_at')->first();
        $this->assertNull($clienteEliminado);

        // Verificar que el pedido sigue existiendo (no se elimina en cascada por soft delete)
        $pedido = DB::table('pedidos')->where('id', $pedidoId)->first();
        $this->assertNotNull($pedido);

        // Verificar JOIN con cliente soft deleted
        $pedidoConCliente = DB::table('pedidos')
            ->leftJoin('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
            ->where('pedidos.id', $pedidoId)
            ->select('pedidos.*', 'clientes.nombre as cliente_nombre', 'clientes.deleted_at as cliente_eliminado')
            ->first();

        $this->assertNotNull($pedidoConCliente);
        $this->assertNotNull($pedidoConCliente->cliente_eliminado); // Cliente está eliminado
        $this->assertEquals('Cliente Soft Delete', $pedidoConCliente->cliente_nombre);
    }

    public function test_pedidos_soft_delete(): void
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
            'nombre' => 'Cliente Pedido SD',
            'direccion' => 'Dir 456',
            'telefono' => '987654321',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto = DB::table('productos')->insertGetId([
            'nombre' => 'Producto SD',
            'sku' => 'SD-PROD-001',
            'precio_base_l1' => 200.00,
            'stock_actual' => 20,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Crear pedido
        $pedidoId = DB::table('pedidos')->insertGetId([
            'cliente_id' => $cliente,
            'monto_bruto' => 600.00,
            'monto_final' => 540.00,
            'estado' => 'confirmado',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Crear item del pedido
        $itemId = DB::table('pedido_items')->insertGetId([
            'pedido_id' => $pedidoId,
            'producto_id' => $producto,
            'cantidad' => 3,
            'precio_unit_l1' => 200.00,
            'subtotal' => 600.00,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Soft delete del pedido
        DB::table('pedidos')
            ->where('id', $pedidoId)
            ->update(['deleted_at' => now()]);

        // Verificar que el pedido no aparece en consultas normales
        $pedidoEliminado = DB::table('pedidos')->where('id', $pedidoId)->whereNull('deleted_at')->first();
        $this->assertNull($pedidoEliminado);

        // Verificar que los items del pedido siguen existiendo
        // (en la vida real podrías querer eliminarlos también, pero para auditoría es mejor conservarlos)
        $item = DB::table('pedido_items')->where('id', $itemId)->first();
        $this->assertNotNull($item);

        // Consulta con pedidos incluyendo eliminados
        $todosLosPedidos = DB::table('pedidos')->where('cliente_id', $cliente)->get();
        $this->assertCount(1, $todosLosPedidos); // Incluye el eliminado

        $pedidosSinEliminados = DB::table('pedidos')
            ->where('cliente_id', $cliente)
            ->whereNull('deleted_at')
            ->get();
        $this->assertCount(0, $pedidosSinEliminados); // No incluye el eliminado
    }

    public function test_soft_delete_auditoria(): void
    {
        // Crear producto
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Auditoría',
            'sku' => 'AUDIT-001',
            'precio_base_l1' => 150.00,
            'stock_actual' => 15,
            'es_combo' => false,
            'marca_producto' => 'BAMBU',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log del sistema antes de eliminar
        $logCreacion = DB::table('system_logs')->insertGetId([
            'modelo' => 'productos',
            'modelo_id' => $productoId,
            'accion' => 'CREATE',
            'datos_anteriores' => null,
            'datos_nuevos' => json_encode([
                'nombre' => 'Producto Auditoría',
                'sku' => 'AUDIT-001',
                'precio_base_l1' => 150.00
            ]),
            'user_id' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Soft delete
        $fechaEliminacion = now();
        DB::table('productos')
            ->where('id', $productoId)
            ->update(['deleted_at' => $fechaEliminacion]);

        // Log del soft delete
        $logEliminacion = DB::table('system_logs')->insertGetId([
            'modelo' => 'productos',
            'modelo_id' => $productoId,
            'accion' => 'SOFT_DELETE',
            'datos_anteriores' => json_encode([
                'deleted_at' => null
            ]),
            'datos_nuevos' => json_encode([
                'deleted_at' => $fechaEliminacion->toISOString()
            ]),
            'user_id' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Verificar historial completo del producto
        $historial = DB::table('system_logs')
            ->where('modelo', 'productos')
            ->where('modelo_id', $productoId)
            ->orderBy('created_at')
            ->get();

        $this->assertCount(2, $historial);
        $this->assertEquals('CREATE', $historial[0]->accion);
        $this->assertEquals('SOFT_DELETE', $historial[1]->accion);

        // Verificar que podemos consultar datos históricos
        $producto = DB::table('productos')->where('id', $productoId)->first();
        $this->assertNotNull($producto);
        $this->assertNotNull($producto->deleted_at);
        $this->assertEquals('Producto Auditoría', $producto->nombre);
    }

    public function test_bulk_soft_delete(): void
    {
        // Crear múltiples productos de la marca SAPHIRUS
        $productos = [];
        for ($i = 1; $i <= 5; $i++) {
            $productos[] = [
                'nombre' => "Producto SAPHIRUS {$i}",
                'sku' => "SAPH-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'precio_base_l1' => 100.00 * $i,
                'stock_actual' => 10 * $i,
                'es_combo' => false,
                'marca_producto' => 'SAPHIRUS',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('productos')->insert($productos);

        // Crear algunos productos BAMBU que no se deben eliminar
        DB::table('productos')->insert([
            'nombre' => 'Producto BAMBU',
            'sku' => 'BAMBU-001',
            'precio_base_l1' => 200.00,
            'stock_actual' => 20,
            'es_combo' => false,
            'marca_producto' => 'BAMBU',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Verificar conteo inicial
        $totalProductos = DB::table('productos')->whereNull('deleted_at')->count();
        $this->assertEquals(6, $totalProductos); // 5 SAPHIRUS + 1 BAMBU

        // Soft delete masivo de productos SAPHIRUS
        DB::table('productos')
            ->where('marca_producto', 'SAPHIRUS')
            ->update(['deleted_at' => now()]);

        // Verificar que solo quedan los BAMBU
        $productosActivos = DB::table('productos')->whereNull('deleted_at')->count();
        $this->assertEquals(1, $productosActivos);

        $productosBambu = DB::table('productos')
            ->where('marca_producto', 'BAMBU')
            ->whereNull('deleted_at')
            ->count();
        $this->assertEquals(1, $productosBambu);

        // Verificar que los SAPHIRUS están soft deleted
        $productosSaphirusEliminados = DB::table('productos')
            ->where('marca_producto', 'SAPHIRUS')
            ->whereNotNull('deleted_at')
            ->count();
        $this->assertEquals(5, $productosSaphirusEliminados);

        // Verificar que el total de productos (incluyendo eliminados) sigue siendo 6
        $totalConEliminados = DB::table('productos')->count();
        $this->assertEquals(6, $totalConEliminados);
    }

    public function test_restore_soft_deleted_records(): void
    {
        // Crear y eliminar producto
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Restore',
            'sku' => 'RESTORE-001',
            'precio_base_l1' => 175.00,
            'stock_actual' => 25,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Soft delete
        DB::table('productos')
            ->where('id', $productoId)
            ->update(['deleted_at' => now()]);

        $this->assertNull(
            DB::table('productos')->where('id', $productoId)->whereNull('deleted_at')->first()
        );

        // Restore (quitar soft delete)
        DB::table('productos')
            ->where('id', $productoId)
            ->update(['deleted_at' => null, 'updated_at' => now()]);

        // Verificar que el producto está activo otra vez
        $productoRestaurado = DB::table('productos')->where('id', $productoId)->whereNull('deleted_at')->first();
        $this->assertNotNull($productoRestaurado);
        $this->assertNull($productoRestaurado->deleted_at);
        $this->assertEquals('Producto Restore', $productoRestaurado->nombre);
    }
}
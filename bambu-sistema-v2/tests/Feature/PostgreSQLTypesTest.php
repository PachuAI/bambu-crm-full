<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostgreSQLTypesTest extends TestCase
{
    use RefreshDatabase;

    public function test_decimal_precision_and_scale(): void
    {
        // Test decimal(12,2) para precios
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Test Decimal',
            'sku' => 'DEC-001',
            'precio_base_l1' => 1234567890.99, // Máximo para decimal(12,2)
            'stock_actual' => 1,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto = DB::table('productos')->where('id', $productoId)->first();
        $this->assertEquals('1234567890.99', $producto->precio_base_l1);

        // Test decimal(8,3) para peso_kg
        DB::table('productos')
            ->where('id', $productoId)
            ->update(['peso_kg' => 12345.678]); // Máximo para decimal(8,3)

        $productoActualizado = DB::table('productos')->where('id', $productoId)->first();
        $this->assertEquals('12345.678', $productoActualizado->peso_kg);
    }

    public function test_varchar_length_limits(): void
    {
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

        // Test varchar(150) para nombre de producto
        $nombreLargo = str_repeat('A', 150);
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => $nombreLargo,
            'sku' => 'VARCHAR-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 1,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto = DB::table('productos')->where('id', $productoId)->first();
        $this->assertEquals(150, strlen($producto->nombre));
        $this->assertEquals($nombreLargo, $producto->nombre);

        // Test varchar(100) para nombre de cliente
        $nombreCliente = str_repeat('B', 100);
        $clienteId = DB::table('clientes')->insertGetId([
            'nombre' => $nombreCliente,
            'direccion' => 'Dir 123',
            'telefono' => '123456789',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $cliente = DB::table('clientes')->where('id', $clienteId)->first();
        $this->assertEquals(100, strlen($cliente->nombre));

        // Test varchar(255) para dirección
        $direccionLarga = str_repeat('C', 255);
        DB::table('clientes')
            ->where('id', $clienteId)
            ->update(['direccion' => $direccionLarga]);

        $clienteActualizado = DB::table('clientes')->where('id', $clienteId)->first();
        $this->assertEquals(255, strlen($clienteActualizado->direccion));
    }

    public function test_text_type(): void
    {
        // Test text para descripción de producto (sin límite de longitud)
        $descripcionLarga = str_repeat('Lorem ipsum dolor sit amet. ', 1000); // ~28,000 caracteres
        
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Text',
            'sku' => 'TEXT-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 1,
            'es_combo' => false,
            'descripcion' => $descripcionLarga,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto = DB::table('productos')->where('id', $productoId)->first();
        $this->assertEquals($descripcionLarga, $producto->descripcion);
        $this->assertGreaterThan(25000, strlen($producto->descripcion));
    }

    public function test_boolean_type(): void
    {
        // Test boolean para es_combo
        $productoTrue = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Boolean True',
            'sku' => 'BOOL-TRUE',
            'precio_base_l1' => 100.00,
            'stock_actual' => 1,
            'es_combo' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $productoFalse = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Boolean False',
            'sku' => 'BOOL-FALSE',
            'precio_base_l1' => 100.00,
            'stock_actual' => 1,
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $prodTrue = DB::table('productos')->where('id', $productoTrue)->first();
        $prodFalse = DB::table('productos')->where('id', $productoFalse)->first();

        $this->assertTrue((bool)$prodTrue->es_combo);
        $this->assertFalse((bool)$prodFalse->es_combo);
    }

    public function test_unsigned_integer_type(): void
    {
        // Test unsignedInteger para stock_actual
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Stock',
            'sku' => 'STOCK-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 4294967295, // Máximo valor para unsigned int
            'es_combo' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto = DB::table('productos')->where('id', $productoId)->first();
        $this->assertEquals(4294967295, $producto->stock_actual);

        // Valor 0 debe ser válido
        DB::table('productos')
            ->where('id', $productoId)
            ->update(['stock_actual' => 0]);

        $productoActualizado = DB::table('productos')->where('id', $productoId)->first();
        $this->assertEquals(0, $productoActualizado->stock_actual);
    }

    public function test_enum_type(): void
    {
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Mendoza',
            'codigo' => 'MZ',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'Mendoza Capital',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $cliente = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Enum',
            'direccion' => 'Dir 123',
            'telefono' => '123',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Test todos los valores del enum estado
        $estadosEnum = ['borrador', 'confirmado', 'en_reparto', 'entregado', 'cancelado'];
        
        foreach ($estadosEnum as $estado) {
            $pedidoId = DB::table('pedidos')->insertGetId([
                'cliente_id' => $cliente,
                'monto_bruto' => 100.00,
                'monto_final' => 100.00,
                'estado' => $estado,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $pedido = DB::table('pedidos')->where('id', $pedidoId)->first();
            $this->assertEquals($estado, $pedido->estado);
        }
    }

    public function test_json_type(): void
    {
        // Test JSON en system_logs para datos_anteriores y datos_nuevos
        $datosAnteriores = [
            'nombre' => 'Producto Viejo',
            'precio' => 100.00,
            'stock' => 10
        ];

        $datosNuevos = [
            'nombre' => 'Producto Nuevo',
            'precio' => 150.00,
            'stock' => 15,
            'metadata' => [
                'categoria' => 'electronica',
                'proveedor' => 'Proveedor ABC'
            ]
        ];

        $logId = DB::table('system_logs')->insertGetId([
            'modelo' => 'productos',
            'modelo_id' => 1,
            'accion' => 'UPDATE',
            'datos_anteriores' => json_encode($datosAnteriores),
            'datos_nuevos' => json_encode($datosNuevos),
            'user_id' => null,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 Test',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $log = DB::table('system_logs')->where('id', $logId)->first();
        
        $anterioresRecuperados = json_decode($log->datos_anteriores, true);
        $nuevosRecuperados = json_decode($log->datos_nuevos, true);

        $this->assertEquals($datosAnteriores, $anterioresRecuperados);
        $this->assertEquals($datosNuevos, $nuevosRecuperados);
        $this->assertEquals('electronica', $nuevosRecuperados['metadata']['categoria']);
    }

    public function test_date_type(): void
    {
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Santa Fe',
            'codigo' => 'SF',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'Santa Fe Capital',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $cliente = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Date',
            'direccion' => 'Dir 123',
            'telefono' => '123',
            'ciudad_id' => $ciudad,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Test date para fecha_reparto
        $fechaReparto = '2025-12-31';
        $pedidoId = DB::table('pedidos')->insertGetId([
            'cliente_id' => $cliente,
            'monto_bruto' => 100.00,
            'monto_final' => 100.00,
            'estado' => 'confirmado',
            'fecha_reparto' => $fechaReparto,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $pedido = DB::table('pedidos')->where('id', $pedidoId)->first();
        $this->assertEquals($fechaReparto, $pedido->fecha_reparto);

        // Test que acepta NULL
        DB::table('pedidos')
            ->where('id', $pedidoId)
            ->update(['fecha_reparto' => null]);

        $pedidoActualizado = DB::table('pedidos')->where('id', $pedidoId)->first();
        $this->assertNull($pedidoActualizado->fecha_reparto);
    }

    public function test_timestamp_with_timezone(): void
    {
        // Test timestamps (created_at, updated_at) con timezone
        $fechaEspecifica = '2025-08-07 15:30:45';
        
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Timestamp',
            'sku' => 'TIMESTAMP-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 1,
            'es_combo' => false,
            'created_at' => $fechaEspecifica,
            'updated_at' => $fechaEspecifica,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto = DB::table('productos')->where('id', $productoId)->first();
        
        $this->assertNotNull($producto->created_at);
        $this->assertNotNull($producto->updated_at);
        
        // Verificar formato timestamp
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
            $producto->created_at
        );
    }

    public function test_nullable_constraints(): void
    {
        // Test campos nullable
        $productoId = DB::table('productos')->insertGetId([
            'nombre' => 'Producto Nullable',
            'sku' => 'NULL-001',
            'precio_base_l1' => 100.00,
            'stock_actual' => 1,
            'es_combo' => false,
            // marca_producto, descripcion, peso_kg son nullable
            'marca_producto' => null,
            'descripcion' => null,
            'peso_kg' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $producto = DB::table('productos')->where('id', $productoId)->first();
        $this->assertNull($producto->marca_producto);
        $this->assertNull($producto->descripcion);
        $this->assertNull($producto->peso_kg);

        // Test email nullable en clientes
        $provincia = DB::table('provincias')->insertGetId([
            'nombre' => 'Tucumán',
            'codigo' => 'TU',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $ciudad = DB::table('ciudades')->insertGetId([
            'nombre' => 'San Miguel de Tucumán',
            'provincia_id' => $provincia,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $clienteId = DB::table('clientes')->insertGetId([
            'nombre' => 'Cliente Sin Email',
            'direccion' => 'Dir 123',
            'telefono' => '123',
            'ciudad_id' => $ciudad,
            'email' => null, // nullable
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $cliente = DB::table('clientes')->where('id', $clienteId)->first();
        $this->assertNull($cliente->email);
    }
}
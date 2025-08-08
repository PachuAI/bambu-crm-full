<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function getExpectedTables(): array
    {
        return [
            'users',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'personal_access_tokens',
            'provincias',
            'ciudades',
            'niveles_descuento',
            'productos',
            'clientes',
            'pedidos',
            'pedido_items',
            'movimiento_stocks',
            'vehiculos',
            'repartos',
            'system_logs',
            'configuraciones',
        ];
    }

    public function test_all_expected_tables_exist(): void
    {
        foreach ($this->getExpectedTables() as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "La tabla '{$table}' no existe en la base de datos"
            );
        }
    }

    public function test_productos_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('productos'));

        $columns = [
            'id', 'nombre', 'sku', 'precio_base_l1', 'stock_actual',
            'es_combo', 'marca_producto', 'descripcion', 'peso_kg',
            'created_at', 'updated_at', 'deleted_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('productos', $column),
                "La columna '{$column}' no existe en la tabla productos"
            );
        }
    }

    public function test_clientes_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('clientes'));

        $columns = [
            'id', 'nombre', 'direccion', 'telefono', 'email', 'ciudad_id',
            'created_at', 'updated_at', 'deleted_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('clientes', $column),
                "La columna '{$column}' no existe en la tabla clientes"
            );
        }
    }

    public function test_pedidos_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('pedidos'));

        $columns = [
            'id', 'cliente_id', 'nivel_descuento_id', 'monto_bruto',
            'monto_final', 'estado', 'fecha_reparto',
            'created_at', 'updated_at', 'deleted_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('pedidos', $column),
                "La columna '{$column}' no existe en la tabla pedidos"
            );
        }
    }

    public function test_pedido_items_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('pedido_items'));

        $columns = [
            'id', 'pedido_id', 'producto_id', 'cantidad', 'precio_unit_l1',
            'subtotal', 'created_at', 'updated_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('pedido_items', $column),
                "La columna '{$column}' no existe en la tabla pedido_items"
            );
        }
    }

    public function test_system_logs_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('system_logs'));

        $columns = [
            'id', 'user_id', 'accion', 'modelo', 'modelo_id', 'datos_anteriores',
            'datos_nuevos', 'ip_address', 'user_agent',
            'created_at', 'updated_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('system_logs', $column),
                "La columna '{$column}' no existe en la tabla system_logs"
            );
        }
    }

    public function test_configuraciones_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('configuraciones'));

        $columns = [
            'id', 'clave', 'valor', 'tipo', 'descripcion', 'categoria',
            'es_publico', 'created_at', 'updated_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('configuraciones', $column),
                "La columna '{$column}' no existe en la tabla configuraciones"
            );
        }
    }
}

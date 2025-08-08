<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StockMovimientosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuario administrador
        $usuario = \App\Models\User::first();
        if (! $usuario) {
            $this->command->warn('No hay usuarios en la base de datos. Saltando StockMovimientosSeeder.');

            return;
        }

        // Obtener algunos productos para generar movimientos
        $productos = \App\Models\Producto::take(5)->get();

        if ($productos->isEmpty()) {
            $this->command->warn('No hay productos en la base de datos. Ejecuta ProductosSeeder primero.');

            return;
        }

        $this->command->info('Generando movimientos de stock de ejemplo...');

        foreach ($productos as $producto) {
            // Simular ingreso de producción inicial
            \App\Models\StockMovimiento::create([
                'producto_id' => $producto->id,
                'tipo' => 'produccion',
                'cantidad' => $producto->stock_actual,
                'stock_anterior' => 0,
                'stock_nuevo' => $producto->stock_actual,
                'motivo' => 'Stock inicial - Lote #2025-001',
                'usuario_id' => $usuario->id,
                'lote_produccion' => '2025-001',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            // Simular algunas ventas
            if ($producto->stock_actual > 10) {
                $ventasCantidad = rand(3, 8);
                \App\Models\StockMovimiento::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'venta',
                    'cantidad' => $ventasCantidad,
                    'stock_anterior' => $producto->stock_actual,
                    'stock_nuevo' => $producto->stock_actual - $ventasCantidad,
                    'motivo' => 'Venta - Pedido #'.rand(1, 50),
                    'usuario_id' => $usuario->id,
                    'created_at' => now()->subDays(rand(1, 8)),
                    'updated_at' => now()->subDays(rand(1, 8)),
                ]);

                // Actualizar stock actual del producto
                $producto->update(['stock_actual' => $producto->stock_actual - $ventasCantidad]);
            }

            // Para algunos productos, simular ajustes negativos
            if (rand(1, 3) == 1 && $producto->stock_actual > 5) {
                $ajuste = rand(2, 5);
                \App\Models\StockMovimiento::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'ajuste_negativo',
                    'cantidad' => $ajuste,
                    'stock_anterior' => $producto->stock_actual,
                    'stock_nuevo' => $producto->stock_actual - $ajuste,
                    'motivo' => 'Producto defectuoso encontrado en inventario',
                    'usuario_id' => $usuario->id,
                    'created_at' => now()->subDays(rand(1, 5)),
                    'updated_at' => now()->subDays(rand(1, 5)),
                ]);

                // Actualizar stock actual del producto
                $producto->update(['stock_actual' => $producto->stock_actual - $ajuste]);
            }
        }

        // Marcar algunos productos para fabricar
        \App\Models\Producto::whereIn('id', $productos->take(2)->pluck('id'))
            ->update(['fabricar_siguiente' => true]);

        // Configurar stock mínimo para algunos productos
        \App\Models\Producto::whereIn('id', $productos->pluck('id'))
            ->update(['stock_minimo' => 10]);

        $this->command->info('✓ Movimientos de stock generados correctamente');
        $this->command->info('✓ '.\App\Models\StockMovimiento::count().' movimientos creados');
        $this->command->info('✓ '.\App\Models\Producto::where('fabricar_siguiente', true)->count().' productos marcados para fabricar');
    }
}

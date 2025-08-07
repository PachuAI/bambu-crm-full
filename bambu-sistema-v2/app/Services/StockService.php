<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\StockMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class StockService
{
    /**
     * Ajusta el stock de un producto y registra el movimiento
     */
    public function ajustarStock(
        Producto $producto,
        int $nuevaCantidad,
        string $tipo,
        ?string $motivo = null,
        ?int $pedidoId = null,
        ?string $loteProduccion = null
    ): StockMovimiento {
        return DB::transaction(function () use ($producto, $nuevaCantidad, $tipo, $motivo, $pedidoId, $loteProduccion) {
            
            $stockAnterior = $producto->stock_actual;
            $diferencia = $nuevaCantidad - $stockAnterior;
            
            // Validar que tenemos motivo para ajustes negativos
            if ($diferencia < 0 && in_array($tipo, ['ajuste_negativo']) && empty($motivo)) {
                throw new Exception('El motivo es obligatorio para ajustes negativos de stock');
            }
            
            // Actualizar el stock del producto
            $producto->update(['stock_actual' => $nuevaCantidad]);
            
            // Registrar el movimiento
            $movimiento = StockMovimiento::create([
                'producto_id' => $producto->id,
                'tipo' => $tipo,
                'cantidad' => abs($diferencia),
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $nuevaCantidad,
                'motivo' => $motivo,
                'usuario_id' => Auth::id(),
                'pedido_id' => $pedidoId,
                'lote_produccion' => $loteProduccion,
            ]);
            
            return $movimiento;
        });
    }
    
    /**
     * Incrementa el stock (ingreso de producción, devoluciones, etc.)
     */
    public function incrementarStock(
        Producto $producto,
        int $cantidad,
        string $motivo = 'Ingreso de producción',
        ?string $loteProduccion = null
    ): StockMovimiento {
        $nuevoStock = $producto->stock_actual + $cantidad;
        
        return $this->ajustarStock(
            $producto,
            $nuevoStock,
            'produccion',
            $motivo,
            null,
            $loteProduccion
        );
    }
    
    /**
     * Decrementa el stock (ventas, ajustes, mermas, etc.)
     */
    public function decrementarStock(
        Producto $producto,
        int $cantidad,
        string $tipo = 'ajuste_negativo',
        string $motivo = null,
        ?int $pedidoId = null
    ): StockMovimiento {
        if (empty($motivo)) {
            throw new Exception('El motivo es obligatorio para decrementos de stock');
        }
        
        $nuevoStock = max(0, $producto->stock_actual - $cantidad);
        
        return $this->ajustarStock(
            $producto,
            $nuevoStock,
            $tipo,
            $motivo,
            $pedidoId
        );
    }
    
    /**
     * Procesar venta (decremento automático por pedido)
     */
    public function procesarVenta(Producto $producto, int $cantidad, int $pedidoId): StockMovimiento
    {
        return $this->ajustarStock(
            $producto,
            $producto->stock_actual - $cantidad,
            'venta',
            "Venta - Pedido #{$pedidoId}",
            $pedidoId
        );
    }
    
    /**
     * Obtener productos con stock bajo
     */
    public function getProductosStockBajo(): \Illuminate\Database\Eloquent\Collection
    {
        return Producto::stockBajo()
            ->with(['stockMovimientos' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get();
    }
    
    /**
     * Obtener productos para fabricar (stock bajo + marcados para fabricación)
     */
    public function getProductosParaFabricar(): \Illuminate\Database\Eloquent\Collection
    {
        return Producto::paraFabricar()
            ->with(['stockMovimientos' => function($query) {
                $query->latest()->limit(3);
            }])
            ->orderBy('stock_actual', 'asc')
            ->get();
    }
    
    /**
     * Obtener historial de movimientos de un producto
     */
    public function getHistorialProducto(Producto $producto, int $limite = 50): \Illuminate\Database\Eloquent\Collection
    {
        return $producto->stockMovimientos()
            ->with(['usuario:id,name', 'pedido:id'])
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }
    
    /**
     * Marcar producto para fabricar en siguiente lote
     */
    public function marcarParaFabricar(Producto $producto, bool $fabricar = true): bool
    {
        return $producto->update(['fabricar_siguiente' => $fabricar]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAjustarRequest;
use App\Http\Requests\StockIngresoRequest;
use App\Models\Producto;
use App\Services\StockService;
use App\Traits\HasStandardPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockController extends Controller
{
    use HasStandardPagination;
    
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Lista de productos con información de stock
     */
    public function index(Request $request): JsonResponse
    {
        $query = Producto::select([
            'id', 'nombre', 'sku', 'stock_actual', 'stock_minimo', 
            'fabricar_siguiente', 'marca_producto', 'precio_base_l1'
        ]);
        
        $paginator = $this->paginateQuery(
            $query,
            $request,
            ['nombre', 'sku', 'stock_actual', 'stock_minimo', 'created_at'],
            [
                'stock_bajo' => function($query, $value) {
                    if ($value) $query->stockBajo();
                },
                'sin_stock' => function($query, $value) {
                    if ($value) $query->where('stock_actual', 0);
                },
                'marca' => 'marca_producto'
            ],
            20,
            50
        );
        
        // Agregar estado de stock a cada producto
        $paginator->getCollection()->transform(function ($producto) {
            $producto->estado_stock = $producto->estado_stock;
            return $producto;
        });
        
        return $this->paginatedResponse($paginator);
    }

    protected function applySearch(Builder $query, string $term): void
    {
        $query->where(function($q) use ($term) {
            $q->where('nombre', 'LIKE', "%{$term}%")
              ->orWhere('sku', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Ajustar stock manualmente (incrementar o decrementar)
     */
    public function ajustar(StockAjustarRequest $request): JsonResponse
    {
        try {
            $producto = Producto::findOrFail($request->producto_id);
            $stockAnterior = $producto->stock_actual;
            $nuevaCantidad = $request->nueva_cantidad;
            
            $tipo = $nuevaCantidad > $stockAnterior ? 'ajuste_positivo' : 'ajuste_negativo';
            
            $movimiento = $this->stockService->ajustarStock(
                $producto,
                $nuevaCantidad,
                $tipo,
                $request->motivo,
                null,
                $request->lote_produccion
            );

            return response()->json([
                'message' => 'Stock ajustado correctamente',
                'movimiento' => $movimiento->load(['producto:id,nombre,sku', 'usuario:id,name']),
                'producto' => [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'stock_anterior' => $stockAnterior,
                    'stock_actual' => $producto->fresh()->stock_actual,
                    'estado_stock' => $producto->fresh()->estado_stock
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al ajustar stock',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Ingreso de stock por producción
     */
    public function ingreso(StockIngresoRequest $request): JsonResponse
    {
        try {
            $producto = Producto::findOrFail($request->producto_id);
            $stockAnterior = $producto->stock_actual;
            
            $movimiento = $this->stockService->incrementarStock(
                $producto,
                $request->cantidad,
                $request->motivo ?? 'Ingreso de producción',
                $request->lote_produccion
            );

            return response()->json([
                'message' => 'Stock incrementado correctamente',
                'movimiento' => $movimiento->load(['producto:id,nombre,sku', 'usuario:id,name']),
                'producto' => [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'stock_anterior' => $stockAnterior,
                    'stock_actual' => $producto->fresh()->stock_actual,
                    'estado_stock' => $producto->fresh()->estado_stock
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al ingresar stock',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Historial de movimientos de un producto
     */
    public function historial(Producto $producto, Request $request): JsonResponse
    {
        $limite = $request->get('limite', 50);
        
        $movimientos = $this->stockService->getHistorialProducto($producto, $limite);
        
        return response()->json([
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'sku' => $producto->sku,
                'stock_actual' => $producto->stock_actual,
                'stock_minimo' => $producto->stock_minimo,
                'estado_stock' => $producto->estado_stock
            ],
            'movimientos' => $movimientos
        ]);
    }

    /**
     * Productos con stock bajo (alertas)
     */
    public function alertas(): JsonResponse
    {
        $productosStockBajo = $this->stockService->getProductosStockBajo();
        
        return response()->json([
            'productos_stock_bajo' => $productosStockBajo->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'sku' => $producto->sku,
                    'stock_actual' => $producto->stock_actual,
                    'stock_minimo' => $producto->stock_minimo,
                    'estado_stock' => $producto->estado_stock,
                    'dias_stock_restante' => $producto->dias_stock_restante,
                    'fabricar_siguiente' => $producto->fabricar_siguiente,
                ];
            }),
            'total' => $productosStockBajo->count()
        ]);
    }

    /**
     * Productos para fabricar
     */
    public function paraFabricar(): JsonResponse
    {
        $productos = $this->stockService->getProductosParaFabricar();
        
        return response()->json([
            'productos_para_fabricar' => $productos->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'sku' => $producto->sku,
                    'stock_actual' => $producto->stock_actual,
                    'stock_minimo' => $producto->stock_minimo,
                    'estado_stock' => $producto->estado_stock,
                    'fabricar_siguiente' => $producto->fabricar_siguiente,
                    'prioridad' => $producto->stock_actual <= 0 ? 'alta' : 
                                 ($producto->stock_actual <= $producto->stock_minimo ? 'media' : 'baja'),
                ];
            }),
            'total' => $productos->count()
        ]);
    }

    /**
     * Marcar/desmarcar producto para fabricar
     */
    public function marcarFabricar(Producto $producto, Request $request): JsonResponse
    {
        $fabricar = $request->get('fabricar', true);
        
        $this->stockService->marcarParaFabricar($producto, $fabricar);
        
        return response()->json([
            'message' => $fabricar ? 
                'Producto marcado para fabricar en próximo lote' : 
                'Producto desmarcado de fabricación',
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'fabricar_siguiente' => $producto->fresh()->fabricar_siguiente
            ]
        ]);
    }
}

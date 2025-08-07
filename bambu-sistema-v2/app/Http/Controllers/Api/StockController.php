<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
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
        $query = Producto::query();
        
        // Filtros
        if ($request->has('stock_bajo') && $request->stock_bajo) {
            $query->stockBajo();
        }
        
        if ($request->has('sin_stock') && $request->sin_stock) {
            $query->where('stock_actual', 0);
        }
        
        if ($request->has('marca')) {
            $query->porMarca($request->marca);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }
        
        $productos = $query->select([
                'id', 'nombre', 'sku', 'stock_actual', 'stock_minimo', 
                'fabricar_siguiente', 'marca_producto', 'precio_base_l1'
            ])
            ->orderBy('nombre')
            ->paginate($request->get('per_page', 20));
        
        // Agregar estado de stock a cada producto
        $productos->getCollection()->transform(function ($producto) {
            $producto->estado_stock = $producto->estado_stock;
            return $producto;
        });
        
        return response()->json($productos);
    }

    /**
     * Ajustar stock manualmente (incrementar o decrementar)
     */
    public function ajustar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|exists:productos,id',
            'nueva_cantidad' => 'required|integer|min:0',
            'motivo' => 'required|string|max:500',
            'lote_produccion' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

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
    public function ingreso(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'string|max:500',
            'lote_produccion' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

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

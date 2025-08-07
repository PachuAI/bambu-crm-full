<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Producto::query();

        // Filtros
        if ($request->has('marca')) {
            $query->where('marca_producto', $request->marca);
        }

        if ($request->has('en_stock') && $request->en_stock == 'true') {
            $query->enStock();
        }

        if ($request->has('es_combo') && $request->es_combo == 'true') {
            $query->combo();
        }

        // Búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        // Paginación
        $productos = $query->orderBy('nombre')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $productos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'sku' => 'required|string|max:50|unique:productos,sku',
            'precio_base_l1' => 'required|numeric|min:0',
            'stock_actual' => 'integer|min:0',
            'es_combo' => 'boolean',
            'marca_producto' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'peso_kg' => 'nullable|numeric|min:0',
        ]);

        $producto = Producto::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $producto,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $producto,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'sku' => ['required', 'string', 'max:50', Rule::unique('productos')->ignore($producto->id)],
            'precio_base_l1' => 'required|numeric|min:0',
            'stock_actual' => 'integer|min:0',
            'es_combo' => 'boolean',
            'marca_producto' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'peso_kg' => 'nullable|numeric|min:0',
        ]);

        $producto->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'data' => $producto->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        $producto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente',
        ]);
    }

    /**
     * Search products by term
     */
    public function search(string $termino): JsonResponse
    {
        $productos = Producto::where(function ($query) use ($termino) {
            $query->where('nombre', 'LIKE', "%{$termino}%")
                  ->orWhere('sku', 'LIKE', "%{$termino}%")
                  ->orWhere('descripcion', 'LIKE', "%{$termino}%");
        })
        ->orderBy('nombre')
        ->limit(20)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $productos,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\Reparto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    public function index(): JsonResponse
    {
        $vehiculos = Vehiculo::with(['repartos' => function($query) {
            $query->where('fecha_programada', today())
                  ->where('estado', '!=', 'entregado');
        }])->get();

        return response()->json([
            'vehiculos' => $vehiculos->map(function($vehiculo) {
                return [
                    'id' => $vehiculo->id,
                    'patente' => $vehiculo->patente,
                    'marca' => $vehiculo->marca,
                    'modelo' => $vehiculo->modelo,
                    'anio' => $vehiculo->anio,
                    'capacidad_kg' => $vehiculo->capacidad_kg,
                    'capacidad_bultos' => $vehiculo->capacidad_bultos,
                    'activo' => $vehiculo->activo,
                    'observaciones' => $vehiculo->observaciones,
                    'nombre_completo' => $vehiculo->nombre_completo,
                    'estado_hoy' => $this->determinarEstadoHoy($vehiculo),
                    'repartos_hoy' => $vehiculo->repartos->count(),
                    'created_at' => $vehiculo->created_at,
                    'updated_at' => $vehiculo->updated_at,
                ];
            })
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patente' => 'required|string|max:10|unique:vehiculos,patente',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'capacidad_kg' => 'required|numeric|min:0',
            'capacidad_bultos' => 'required|numeric|min:0',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);

        $vehiculo = Vehiculo::create($validated);

        return response()->json([
            'message' => 'Vehículo creado exitosamente',
            'vehiculo' => $vehiculo
        ], 201);
    }

    public function show(Vehiculo $vehiculo): JsonResponse
    {
        $vehiculo->load(['repartos.pedido.cliente']);

        $estadisticas = [
            'total_repartos' => $vehiculo->repartos()->count(),
            'repartos_entregados' => $vehiculo->repartos()->where('estado', 'entregado')->count(),
            'repartos_fallidos' => $vehiculo->repartos()->where('estado', 'fallido')->count(),
            'km_totales' => $vehiculo->repartos()->sum('km_recorridos'),
            'repartos_mes_actual' => $vehiculo->repartos()
                ->whereMonth('fecha_programada', now()->month)
                ->whereYear('fecha_programada', now()->year)
                ->count(),
        ];

        return response()->json([
            'vehiculo' => $vehiculo,
            'estadisticas' => $estadisticas
        ]);
    }

    public function update(Request $request, Vehiculo $vehiculo): JsonResponse
    {
        $validated = $request->validate([
            'patente' => [
                'required',
                'string',
                'max:10',
                Rule::unique('vehiculos')->ignore($vehiculo->id)
            ],
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'capacidad_kg' => 'required|numeric|min:0',
            'capacidad_bultos' => 'required|numeric|min:0',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);

        $vehiculo->update($validated);

        return response()->json([
            'message' => 'Vehículo actualizado exitosamente',
            'vehiculo' => $vehiculo
        ]);
    }

    public function destroy(Vehiculo $vehiculo): JsonResponse
    {
        if ($vehiculo->repartos()->where('estado', '!=', 'entregado')->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el vehículo porque tiene repartos activos'
            ], 422);
        }

        $vehiculo->delete();

        return response()->json([
            'message' => 'Vehículo eliminado exitosamente'
        ]);
    }

    public function disponibles(Request $request): JsonResponse
    {
        $fecha = $request->input('fecha', today()->format('Y-m-d'));
        
        $vehiculos = Vehiculo::disponibles($fecha)->get();

        return response()->json([
            'vehiculos' => $vehiculos,
            'fecha' => $fecha
        ]);
    }

    public function activar(Vehiculo $vehiculo): JsonResponse
    {
        $vehiculo->update(['activo' => true]);

        return response()->json([
            'message' => 'Vehículo activado exitosamente',
            'vehiculo' => $vehiculo
        ]);
    }

    public function desactivar(Vehiculo $vehiculo): JsonResponse
    {
        if ($vehiculo->repartos()->whereIn('estado', ['programado', 'en_ruta'])->exists()) {
            return response()->json([
                'message' => 'No se puede desactivar el vehículo porque tiene repartos activos'
            ], 422);
        }

        $vehiculo->update(['activo' => false]);

        return response()->json([
            'message' => 'Vehículo desactivado exitosamente',
            'vehiculo' => $vehiculo
        ]);
    }

    private function determinarEstadoHoy(Vehiculo $vehiculo): string
    {
        if (!$vehiculo->activo) {
            return 'inactivo';
        }

        $repartoHoy = $vehiculo->repartos()
            ->where('fecha_programada', today())
            ->first();

        if (!$repartoHoy) {
            return 'disponible';
        }

        return match($repartoHoy->estado) {
            'programado' => 'programado',
            'en_ruta' => 'en_ruta',
            'entregado' => 'libre',
            'fallido' => 'libre',
            default => 'disponible'
        };
    }
}
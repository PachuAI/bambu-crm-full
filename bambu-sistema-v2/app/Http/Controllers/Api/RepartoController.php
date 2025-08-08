<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Reparto;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepartoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Reparto::with(['pedido.cliente', 'vehiculo']);

        if ($request->has('fecha')) {
            $query->whereDate('fecha_programada', $request->fecha);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        $repartos = $query->orderBy('fecha_programada', 'desc')
            ->orderBy('hora_salida', 'asc')
            ->get();

        return response()->json([
            'repartos' => $repartos,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha_programada' => 'required|date',
            'hora_salida' => 'nullable|date_format:H:i',
            'observaciones' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            // Verificar que el pedido no tenga ya un reparto asignado
            if (Reparto::where('pedido_id', $validated['pedido_id'])->exists()) {
                return response()->json([
                    'message' => 'Este pedido ya tiene un reparto asignado',
                ], 422);
            }

            // Verificar disponibilidad del vehículo
            $vehiculo = Vehiculo::find($validated['vehiculo_id']);
            if (! $vehiculo->activo) {
                return response()->json([
                    'message' => 'El vehículo seleccionado no está activo',
                ], 422);
            }

            $reparto = Reparto::create(array_merge($validated, [
                'estado' => 'programado'
            ]));

            // Actualizar estado del pedido a "Listo para enviar"
            $pedido = Pedido::find($validated['pedido_id']);
            $pedido->update(['estado' => 'listo_envio']);

            $reparto->load(['pedido.cliente', 'vehiculo']);
            
            return response()->json([
                'message' => 'Reparto programado exitosamente',
                'reparto' => $reparto,
            ], 201);
        });
    }

    public function show(Reparto $reparto): JsonResponse
    {
        $reparto->load(['pedido.cliente', 'pedido.items.producto', 'vehiculo']);

        return response()->json([
            'reparto' => $reparto,
        ]);
    }

    public function update(Request $request, Reparto $reparto): JsonResponse
    {
        $validated = $request->validate([
            'vehiculo_id' => 'sometimes|exists:vehiculos,id',
            'fecha_programada' => 'sometimes|date',
            'hora_salida' => 'nullable|date_format:H:i',
            'hora_entrega' => 'nullable|date_format:H:i',
            'observaciones' => 'nullable|string',
            'km_recorridos' => 'nullable|numeric|min:0',
        ]);

        $reparto->update($validated);

        return response()->json([
            'message' => 'Reparto actualizado exitosamente',
            'reparto' => $reparto->load(['pedido.cliente', 'vehiculo']),
        ]);
    }

    public function cambiarEstado(Request $request, Reparto $reparto): JsonResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:programado,en_ruta,entregado,fallido',
            'observaciones' => 'nullable|string',
            'hora_salida' => 'nullable|date_format:H:i',
            'hora_entrega' => 'nullable|date_format:H:i',
            'km_recorridos' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $reparto) {
            $updateData = ['estado' => $validated['estado']];

            // Lógica específica según el nuevo estado
            switch ($validated['estado']) {
                case 'en_ruta':
                    $updateData['hora_salida'] = $validated['hora_salida'] ?? now()->format('H:i');
                    // Actualizar estado del pedido
                    $reparto->pedido->update(['estado' => 'en_transito']);
                    break;

                case 'entregado':
                    $updateData['hora_entrega'] = $validated['hora_entrega'] ?? now()->format('H:i');
                    if (isset($validated['km_recorridos'])) {
                        $updateData['km_recorridos'] = $validated['km_recorridos'];
                    }
                    // Actualizar estado del pedido
                    $reparto->pedido->update(['estado' => 'entregado']);
                    break;

                case 'fallido':
                    $updateData['hora_entrega'] = $validated['hora_entrega'] ?? now()->format('H:i');
                    // Actualizar estado del pedido
                    $reparto->pedido->update(['estado' => 'fallido']);
                    break;
            }

            if (isset($validated['observaciones'])) {
                $updateData['observaciones'] = $validated['observaciones'];
            }

            $reparto->update($updateData);

            return response()->json([
                'message' => 'Estado del reparto actualizado exitosamente',
                'reparto' => $reparto->load(['pedido.cliente', 'vehiculo']),
            ]);
        });
    }

    public function planificacionSemanal(Request $request): JsonResponse
    {
        $fecha = $request->input('fecha', today()->format('Y-m-d'));
        $fechaInicio = Carbon::parse($fecha)->startOfWeek();
        $fechaFin = Carbon::parse($fecha)->endOfWeek();

        $repartos = Reparto::with(['pedido.cliente', 'vehiculo'])
            ->whereDate('fecha_programada', '>=', $fechaInicio->format('Y-m-d'))
            ->whereDate('fecha_programada', '<=', $fechaFin->format('Y-m-d'))
            ->orderBy('fecha_programada')
            ->orderBy('hora_salida')
            ->get();

        $vehiculos = Vehiculo::activos()->get();

        // Organizar por día de la semana
        $planificacion = [];
        for ($i = 0; $i < 7; $i++) {
            $dia = $fechaInicio->copy()->addDays($i);
            $planificacion[$dia->format('Y-m-d')] = [
                'fecha' => $dia->format('Y-m-d'),
                'dia_semana' => $dia->locale('es')->dayName,
                'repartos' => $repartos->filter(function($reparto) use ($dia) {
                    return Carbon::parse($reparto->fecha_programada)->format('Y-m-d') === $dia->format('Y-m-d');
                })->values(),
            ];
        }

        return response()->json([
            'planificacion' => $planificacion,
            'vehiculos' => $vehiculos,
            'semana' => [
                'inicio' => $fechaInicio->format('Y-m-d'),
                'fin' => $fechaFin->format('Y-m-d'),
            ],
        ]);
    }

    public function seguimientoTiempoReal(): JsonResponse
    {
        $hoy = today();

        $repartos = Reparto::with(['pedido.cliente', 'vehiculo'])
            ->where('fecha_programada', $hoy)
            ->orderBy('hora_salida')
            ->get();

        $estadisticas = [
            'total_programados' => $repartos->count(),
            'en_ruta' => $repartos->where('estado', 'en_ruta')->count(),
            'entregados' => $repartos->where('estado', 'entregado')->count(),
            'fallidos' => $repartos->where('estado', 'fallido')->count(),
            'pendientes' => $repartos->where('estado', 'programado')->count(),
        ];

        $vehiculos_activos = Vehiculo::with(['repartos' => function ($query) use ($hoy) {
            $query->where('fecha_programada', $hoy);
        }])->activos()->get();

        return response()->json([
            'repartos' => $repartos,
            'estadisticas' => $estadisticas,
            'vehiculos_activos' => $vehiculos_activos,
            'fecha' => $hoy->format('Y-m-d'),
        ]);
    }

    public function destroy(Reparto $reparto): JsonResponse
    {
        return DB::transaction(function () use ($reparto) {
            if ($reparto->estado === 'entregado') {
                return response()->json([
                    'message' => 'No se puede eliminar un reparto ya entregado',
                ], 422);
            }

            // Revertir estado del pedido a confirmado
            $reparto->pedido->update(['estado' => 'confirmado']);

            $reparto->delete();

            return response()->json([
                'message' => 'Reparto eliminado exitosamente',
            ]);
        });
    }
}

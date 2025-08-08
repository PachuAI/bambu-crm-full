<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Reparto;
use App\Models\Vehiculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $hoy = today();
        $mesActual = now()->month;
        $anioActual = now()->year;

        // Métricas principales
        $metricas = [
            'entregas_hoy' => Reparto::where('fecha_programada', $hoy)
                ->where('estado', 'entregado')
                ->count(),

            'pendientes_entrega' => Reparto::where('fecha_programada', $hoy)
                ->whereIn('estado', ['programado', 'en_ruta'])
                ->count(),

            'vehiculos_activos' => Vehiculo::activos()->count(),

            'efectividad_entregas' => $this->calcularEfectividadEntregas($hoy),

            'km_recorridos_hoy' => Reparto::where('fecha_programada', $hoy)
                ->sum('km_recorridos') ?? 0,
        ];

        return response()->json([
            'metricas' => $metricas,
            'fecha' => $hoy->format('Y-m-d'),
        ]);
    }

    public function reporteVehiculos(Request $request): JsonResponse
    {
        $desde = $request->input('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->endOfMonth()->format('Y-m-d'));

        $vehiculos = Vehiculo::with(['repartos' => function ($query) use ($desde, $hasta) {
            $query->whereDate('fecha_programada', '>=', $desde)
                  ->whereDate('fecha_programada', '<=', $hasta);
        }])->get();

        $reporteVehiculos = $vehiculos->map(function ($vehiculo) {
            $repartos = $vehiculo->repartos;

            return [
                'id' => $vehiculo->id,
                'nombre_completo' => $vehiculo->nombre_completo,
                'total_repartos' => $repartos->count(),
                'repartos_entregados' => $repartos->where('estado', 'entregado')->count(),
                'repartos_fallidos' => $repartos->where('estado', 'fallido')->count(),
                'km_totales' => $repartos->sum('km_recorridos') ?? 0,
                'efectividad' => $repartos->count() > 0
                    ? round(($repartos->where('estado', 'entregado')->count() / $repartos->count()) * 100, 2)
                    : 0,
                'dias_trabajados' => $repartos->pluck('fecha_programada')->unique()->count(),
                'promedio_km_dia' => $repartos->count() > 0
                    ? round(($repartos->sum('km_recorridos') ?? 0) / $repartos->pluck('fecha_programada')->unique()->count(), 2)
                    : 0,
            ];
        });

        return response()->json([
            'vehiculos' => $reporteVehiculos,
            'periodo' => [
                'desde' => $desde,
                'hasta' => $hasta,
            ],
        ]);
    }

    public function reporteEntregas(Request $request): JsonResponse
    {
        $desde = $request->input('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->endOfMonth()->format('Y-m-d'));

        // Estadísticas por día
        $entregasPorDia = Reparto::selectRaw('
                DATE(fecha_programada) as fecha,
                COUNT(*) as total,
                SUM(CASE WHEN estado = "entregado" THEN 1 ELSE 0 END) as entregados,
                SUM(CASE WHEN estado = "fallido" THEN 1 ELSE 0 END) as fallidos,
                SUM(km_recorridos) as km_totales
            ')
            ->whereDate('fecha_programada', '>=', $desde)
            ->whereDate('fecha_programada', '<=', $hasta)
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Estadísticas por cliente
        $entregasPorCliente = Reparto::selectRaw('
                clientes.nombre,
                clientes.id as cliente_id,
                COUNT(*) as total_repartos,
                SUM(CASE WHEN repartos.estado = "entregado" THEN 1 ELSE 0 END) as entregados,
                SUM(CASE WHEN repartos.estado = "fallido" THEN 1 ELSE 0 END) as fallidos
            ')
            ->join('pedidos', 'repartos.pedido_id', '=', 'pedidos.id')
            ->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
            ->whereDate('repartos.fecha_programada', '>=', $desde)
            ->whereDate('repartos.fecha_programada', '<=', $hasta)
            ->groupBy('clientes.id', 'clientes.nombre')
            ->orderByDesc('total_repartos')
            ->get();

        // Resumen general
        $resumen = [
            'total_repartos' => Reparto::whereDate('fecha_programada', '>=', $desde)
                ->whereDate('fecha_programada', '<=', $hasta)->count(),
            'total_entregados' => Reparto::whereDate('fecha_programada', '>=', $desde)
                ->whereDate('fecha_programada', '<=', $hasta)
                ->where('estado', 'entregado')->count(),
            'total_fallidos' => Reparto::whereDate('fecha_programada', '>=', $desde)
                ->whereDate('fecha_programada', '<=', $hasta)
                ->where('estado', 'fallido')->count(),
            'km_totales' => Reparto::whereDate('fecha_programada', '>=', $desde)
                ->whereDate('fecha_programada', '<=', $hasta)
                ->sum('km_recorridos') ?? 0,
            'efectividad_general' => 0,
        ];

        if ($resumen['total_repartos'] > 0) {
            $resumen['efectividad_general'] = round(
                ($resumen['total_entregados'] / $resumen['total_repartos']) * 100,
                2
            );
        }

        return response()->json([
            'resumen' => $resumen,
            'por_dia' => $entregasPorDia,
            'por_cliente' => $entregasPorCliente,
            'periodo' => [
                'desde' => $desde,
                'hasta' => $hasta,
            ],
        ]);
    }

    public function reporteOperativo(Request $request): JsonResponse
    {
        $desde = $request->input('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->endOfMonth()->format('Y-m-d'));

        // Tiempos promedio de entrega - Compatible con SQLite y PostgreSQL
        $repartos = Reparto::whereDate('fecha_programada', '>=', $desde)
            ->whereDate('fecha_programada', '<=', $hasta)
            ->where('estado', 'entregado')
            ->whereNotNull('hora_salida')
            ->whereNotNull('hora_entrega')
            ->get();

        $tiempoPromedioMinutos = 0;
        if ($repartos->count() > 0) {
            $totalMinutos = $repartos->reduce(function ($carry, $reparto) {
                $salida = \Carbon\Carbon::parse($reparto->fecha_programada.' '.$reparto->hora_salida);
                $entrega = \Carbon\Carbon::parse($reparto->fecha_programada.' '.$reparto->hora_entrega);

                return $carry + $entrega->diffInMinutes($salida);
            }, 0);
            $tiempoPromedioMinutos = $totalMinutos / $repartos->count();
        }

        $tiemposEntrega = (object) ['tiempo_promedio_minutos' => $tiempoPromedioMinutos];

        // Capacidad utilizada por vehículo
        $capacidadUtilizada = DB::select('
            SELECT 
                v.id,
                v.patente,
                v.capacidad_kg,
                AVG(peso_utilizado.peso_total) as peso_promedio_utilizado,
                (AVG(peso_utilizado.peso_total) / v.capacidad_kg * 100) as porcentaje_utilizacion
            FROM vehiculos v
            LEFT JOIN (
                SELECT 
                    r.vehiculo_id,
                    r.fecha_programada,
                    SUM(COALESCE(p.peso_kg, 0) * pi.cantidad) as peso_total
                FROM repartos r
                JOIN pedidos ped ON r.pedido_id = ped.id
                JOIN pedido_items pi ON ped.id = pi.pedido_id
                JOIN productos p ON pi.producto_id = p.id
                WHERE r.fecha_programada BETWEEN ? AND ?
                GROUP BY r.vehiculo_id, r.fecha_programada
            ) peso_utilizado ON v.id = peso_utilizado.vehiculo_id
            WHERE v.activo = 1
            GROUP BY v.id, v.patente, v.capacidad_kg
        ', [$desde, $hasta]);

        // Estados de repartos por franja horaria - Compatible con SQLite
        $repartosData = Reparto::whereDate('fecha_programada', '>=', $desde)
            ->whereDate('fecha_programada', '<=', $hasta)
            ->whereNotNull('hora_salida')
            ->get();

        $repartosPorHora = $repartosData->groupBy(function ($reparto) {
            return \Carbon\Carbon::parse($reparto->hora_salida)->format('H');
        })->map(function ($repartos, $hora) {
            return [
                'hora' => intval($hora),
                'total' => $repartos->count(),
                'entregados' => $repartos->where('estado', 'entregado')->count(),
            ];
        })->sortBy('hora')->values();

        return response()->json([
            'tiempo_promedio_entrega' => round($tiemposEntrega->tiempo_promedio_minutos ?? 0, 2),
            'capacidad_vehiculos' => $capacidadUtilizada,
            'repartos_por_hora' => $repartosPorHora,
            'periodo' => [
                'desde' => $desde,
                'hasta' => $hasta,
            ],
        ]);
    }

    private function calcularEfectividadEntregas($fecha): float
    {
        $totalRepartos = Reparto::where('fecha_programada', $fecha)
            ->whereIn('estado', ['entregado', 'fallido'])
            ->count();

        if ($totalRepartos === 0) {
            return 0;
        }

        $repartosEntregados = Reparto::where('fecha_programada', $fecha)
            ->where('estado', 'entregado')
            ->count();

        return round(($repartosEntregados / $totalRepartos) * 100, 2);
    }
}

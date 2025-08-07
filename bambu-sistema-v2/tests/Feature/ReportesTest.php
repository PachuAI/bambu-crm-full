<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Reparto;
use App\Models\Pedido;
use App\Models\Cliente;
use Laravel\Sanctum\Sanctum;

class ReportesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $cliente;
    protected $vehiculo;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->cliente = Cliente::factory()->create();
        $this->vehiculo = Vehiculo::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_obtiene_metricas_dashboard()
    {
        // Crear datos de prueba para hoy
        $pedido1 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido2 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido3 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        // Reparto entregado
        Reparto::create([
            'pedido_id' => $pedido1->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'entregado',
            'km_recorridos' => 25.5
        ]);

        // Reparto en ruta
        Reparto::create([
            'pedido_id' => $pedido2->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'en_ruta'
        ]);

        // Reparto programado
        Reparto::create([
            'pedido_id' => $pedido3->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'programado'
        ]);

        $response = $this->getJson('/api/v1/reportes/dashboard');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'metricas' => [
                        'entregas_hoy',
                        'pendientes_entrega',
                        'vehiculos_activos',
                        'efectividad_entregas',
                        'km_recorridos_hoy'
                    ],
                    'fecha'
                ]);

        $metricas = $response->json('metricas');
        
        $this->assertEquals(1, $metricas['entregas_hoy']);
        $this->assertEquals(2, $metricas['pendientes_entrega']); // en_ruta + programado
        $this->assertEquals(1, $metricas['vehiculos_activos']);
        $this->assertEquals(25.5, $metricas['km_recorridos_hoy']);
    }

    public function test_calcula_efectividad_correctamente()
    {
        $pedido1 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido2 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido3 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        // 2 entregados, 1 fallido = 66.67% efectividad
        Reparto::create([
            'pedido_id' => $pedido1->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'entregado'
        ]);

        Reparto::create([
            'pedido_id' => $pedido2->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'entregado'
        ]);

        Reparto::create([
            'pedido_id' => $pedido3->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'fallido'
        ]);

        $response = $this->getJson('/api/v1/reportes/dashboard');

        $response->assertStatus(200);
        
        $efectividad = $response->json('metricas.efectividad_entregas');
        $this->assertEquals(66.67, $efectividad);
    }

    public function test_obtiene_reporte_vehiculos()
    {
        $vehiculo2 = Vehiculo::factory()->create();
        $pedido1 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido2 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        // Repartos para vehiculo1
        Reparto::create([
            'pedido_id' => $pedido1->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'entregado',
            'km_recorridos' => 30
        ]);

        // Repartos para vehiculo2
        Reparto::create([
            'pedido_id' => $pedido2->id,
            'vehiculo_id' => $vehiculo2->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'fallido',
            'km_recorridos' => 15
        ]);

        $desde = now()->format('Y-m-d');
        $hasta = now()->format('Y-m-d');

        $response = $this->getJson("/api/v1/reportes/vehiculos?desde={$desde}&hasta={$hasta}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'vehiculos' => [
                        '*' => [
                            'id',
                            'nombre_completo',
                            'total_repartos',
                            'repartos_entregados',
                            'repartos_fallidos',
                            'km_totales',
                            'efectividad',
                            'dias_trabajados'
                        ]
                    ],
                    'periodo'
                ]);

        $vehiculos = $response->json('vehiculos');
        
        // Vehiculo 1: 100% efectividad (1 entregado de 1 total)
        $vehiculo1Data = collect($vehiculos)->firstWhere('id', $this->vehiculo->id);
        $this->assertEquals(1, $vehiculo1Data['total_repartos']);
        $this->assertEquals(1, $vehiculo1Data['repartos_entregados']);
        $this->assertEquals(100, $vehiculo1Data['efectividad']);
        $this->assertEquals(30, $vehiculo1Data['km_totales']);

        // Vehiculo 2: 0% efectividad (0 entregados de 1 total)
        $vehiculo2Data = collect($vehiculos)->firstWhere('id', $vehiculo2->id);
        $this->assertEquals(1, $vehiculo2Data['total_repartos']);
        $this->assertEquals(0, $vehiculo2Data['repartos_entregados']);
        $this->assertEquals(0, $vehiculo2Data['efectividad']);
    }

    public function test_obtiene_reporte_entregas()
    {
        $cliente2 = Cliente::factory()->create(['nombre' => 'Cliente Test 2']);
        
        $pedido1 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido2 = Pedido::factory()->create(['cliente_id' => $cliente2->id]);
        $pedido3 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        Reparto::create([
            'pedido_id' => $pedido1->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'entregado'
        ]);

        Reparto::create([
            'pedido_id' => $pedido2->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'entregado'
        ]);

        Reparto::create([
            'pedido_id' => $pedido3->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'fallido'
        ]);

        $desde = now()->format('Y-m-d');
        $hasta = now()->format('Y-m-d');

        $response = $this->getJson("/api/v1/reportes/entregas?desde={$desde}&hasta={$hasta}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'resumen' => [
                        'total_repartos',
                        'total_entregados',
                        'total_fallidos',
                        'km_totales',
                        'efectividad_general'
                    ],
                    'por_dia',
                    'por_cliente',
                    'periodo'
                ]);

        $resumen = $response->json('resumen');
        $this->assertEquals(3, $resumen['total_repartos']);
        $this->assertEquals(2, $resumen['total_entregados']);
        $this->assertEquals(1, $resumen['total_fallidos']);
        $this->assertEquals(66.67, $resumen['efectividad_general']);

        $porCliente = $response->json('por_cliente');
        $this->assertCount(2, $porCliente);
        
        // El cliente con más repartos debería estar primero
        $primerCliente = $porCliente[0];
        $this->assertEquals($this->cliente->nombre, $primerCliente['nombre']);
        $this->assertEquals(2, $primerCliente['total_repartos']);
    }

    public function test_obtiene_reporte_operativo()
    {
        $desde = now()->format('Y-m-d');
        $hasta = now()->format('Y-m-d');

        $response = $this->getJson("/api/v1/reportes/operativo?desde={$desde}&hasta={$hasta}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'tiempo_promedio_entrega',
                    'capacidad_vehiculos',
                    'repartos_por_hora',
                    'periodo'
                ]);
    }

    public function test_reporte_vehiculos_con_fechas_personalizadas()
    {
        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        // Reparto de ayer
        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => now()->subDay(),
            'estado' => 'entregado'
        ]);

        // Reparto de hoy
        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'entregado'
        ]);

        // Solo pedir reporte de hoy
        $desde = now()->format('Y-m-d');
        $hasta = now()->format('Y-m-d');

        $response = $this->getJson("/api/v1/reportes/vehiculos?desde={$desde}&hasta={$hasta}");

        $response->assertStatus(200);
        
        $vehiculos = $response->json('vehiculos');
        $vehiculoData = collect($vehiculos)->firstWhere('id', $this->vehiculo->id);
        
        // Solo debería contar el reparto de hoy
        $this->assertEquals(1, $vehiculoData['total_repartos']);
    }

    public function test_dashboard_sin_datos()
    {
        $response = $this->getJson('/api/v1/reportes/dashboard');

        $response->assertStatus(200);
        
        $metricas = $response->json('metricas');
        $this->assertEquals(0, $metricas['entregas_hoy']);
        $this->assertEquals(0, $metricas['pendientes_entrega']);
        $this->assertEquals(0, $metricas['efectividad_entregas']);
        $this->assertEquals(0, $metricas['km_recorridos_hoy']);
    }

    public function test_efectividad_con_solo_repartos_pendientes()
    {
        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'programado' // Estado pendiente
        ]);

        $response = $this->getJson('/api/v1/reportes/dashboard');

        $response->assertStatus(200);
        
        // No hay repartos finalizados, efectividad debería ser 0
        $efectividad = $response->json('metricas.efectividad_entregas');
        $this->assertEquals(0, $efectividad);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Reparto;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IntegracionLogisticaTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $cliente;

    protected $vehiculo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->cliente = Cliente::factory()->create();
        $this->vehiculo = Vehiculo::factory()->create(['activo' => true]);
        Sanctum::actingAs($this->user);
    }

    public function test_flujo_completo_de_entrega()
    {
        // 1. Crear pedido confirmado
        $pedido = Pedido::factory()->create([
            'cliente_id' => $this->cliente->id,
            'estado' => 'confirmado',
        ]);

        $this->assertEquals('confirmado', $pedido->estado);

        // 2. Programar reparto (planificación)
        $response = $this->postJson('/api/v1/repartos', [
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'hora_salida' => '09:00',
            'observaciones' => 'Entrega matutina',
        ]);

        $response->assertStatus(201);

        $reparto = Reparto::where('pedido_id', $pedido->id)->first();
        $this->assertNotNull($reparto);
        $this->assertEquals('programado', $reparto->estado);

        // El pedido cambió a "listo_envio"
        $pedido->refresh();
        $this->assertEquals('listo_envio', $pedido->estado);

        // 3. Iniciar reparto (seguimiento)
        $response = $this->patchJson("/api/v1/repartos/{$reparto->id}/estado", [
            'estado' => 'en_ruta',
            'hora_salida' => '09:15',
        ]);

        $response->assertStatus(200);

        $reparto->refresh();
        $pedido->refresh();

        $this->assertEquals('en_ruta', $reparto->estado);
        $this->assertEquals('09:15', $reparto->hora_salida);
        $this->assertEquals('en_transito', $pedido->estado);

        // 4. Completar entrega
        $response = $this->patchJson("/api/v1/repartos/{$reparto->id}/estado", [
            'estado' => 'entregado',
            'hora_entrega' => '10:45',
            'km_recorridos' => 25.8,
        ]);

        $response->assertStatus(200);

        $reparto->refresh();
        $pedido->refresh();

        $this->assertEquals('entregado', $reparto->estado);
        $this->assertEquals('10:45', $reparto->hora_entrega);
        $this->assertEquals(25.8, $reparto->km_recorridos);
        $this->assertEquals('entregado', $pedido->estado);

        // 5. Verificar métricas del dashboard
        $response = $this->getJson('/api/v1/reportes/dashboard');

        $response->assertStatus(200);
        $metricas = $response->json('metricas');

        $this->assertEquals(1, $metricas['entregas_hoy']);
        $this->assertEquals(0, $metricas['pendientes_entrega']);
        $this->assertEquals(100, $metricas['efectividad_entregas']);
        $this->assertEquals(25.8, $metricas['km_recorridos_hoy']);
    }

    public function test_flujo_con_entrega_fallida()
    {
        // Crear pedido y reparto en ruta
        $pedido = Pedido::factory()->create([
            'cliente_id' => $this->cliente->id,
            'estado' => 'en_transito',
        ]);

        $reparto = Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'en_ruta',
            'hora_salida' => '09:00',
        ]);

        // Marcar como fallido
        $response = $this->patchJson("/api/v1/repartos/{$reparto->id}/estado", [
            'estado' => 'fallido',
            'hora_entrega' => '10:30',
            'observaciones' => 'Cliente no se encontraba en domicilio',
        ]);

        $response->assertStatus(200);

        $reparto->refresh();
        $pedido->refresh();

        $this->assertEquals('fallido', $reparto->estado);
        $this->assertEquals('fallido', $pedido->estado);
        $this->assertStringContainsString('Cliente no se encontraba', $reparto->observaciones);

        // Verificar métricas
        $response = $this->getJson('/api/v1/reportes/dashboard');
        $metricas = $response->json('metricas');

        $this->assertEquals(0, $metricas['entregas_hoy']);
        $this->assertEquals(0, $metricas['efectividad_entregas']); // 0% porque no hay entregas exitosas
    }

    public function test_planificacion_semanal_multiples_vehiculos()
    {
        $vehiculo2 = Vehiculo::factory()->create(['activo' => true]);

        $pedido1 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido2 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido3 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        // Lunes - Vehículo 1
        Reparto::create([
            'pedido_id' => $pedido1->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => now()->startOfWeek(),
            'estado' => 'programado',
        ]);

        // Miércoles - Vehículo 2
        Reparto::create([
            'pedido_id' => $pedido2->id,
            'vehiculo_id' => $vehiculo2->id,
            'fecha_programada' => now()->startOfWeek()->addDays(2),
            'estado' => 'programado',
        ]);

        // Viernes - Vehículo 1
        Reparto::create([
            'pedido_id' => $pedido3->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => now()->startOfWeek()->addDays(4),
            'estado' => 'programado',
        ]);

        $response = $this->getJson('/api/v1/planificacion-semanal');

        $response->assertStatus(200);

        $planificacion = $response->json('planificacion');
        $vehiculos = $response->json('vehiculos');

        $this->assertCount(7, $planificacion); // 7 días
        $this->assertCount(2, $vehiculos); // 2 vehículos activos

        // Verificar distribución por días
        $lunes = $planificacion[now()->startOfWeek()->format('Y-m-d')];
        $miercoles = $planificacion[now()->startOfWeek()->addDays(2)->format('Y-m-d')];
        $viernes = $planificacion[now()->startOfWeek()->addDays(4)->format('Y-m-d')];

        $this->assertCount(1, $lunes['repartos']);
        $this->assertCount(1, $miercoles['repartos']);
        $this->assertCount(1, $viernes['repartos']);
    }

    public function test_seguimiento_tiempo_real_multiples_estados()
    {
        $cliente2 = Cliente::factory()->create();

        $pedidos = Pedido::factory()->count(5)->create(['cliente_id' => $this->cliente->id]);
        $pedido6 = Pedido::factory()->create(['cliente_id' => $cliente2->id]);

        // Estados diferentes para hoy
        Reparto::create([
            'pedido_id' => $pedidos[0]->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        Reparto::create([
            'pedido_id' => $pedidos[1]->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        Reparto::create([
            'pedido_id' => $pedidos[2]->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'en_ruta',
        ]);

        Reparto::create([
            'pedido_id' => $pedidos[3]->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'entregado',
        ]);

        Reparto::create([
            'pedido_id' => $pedidos[4]->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'fallido',
        ]);

        Reparto::create([
            'pedido_id' => $pedido6->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'entregado',
        ]);

        $response = $this->getJson('/api/v1/seguimiento-tiempo-real');

        $response->assertStatus(200);

        $estadisticas = $response->json('estadisticas');
        $repartos = $response->json('repartos');

        $this->assertEquals(6, $estadisticas['total_programados']);
        $this->assertEquals(1, $estadisticas['en_ruta']);
        $this->assertEquals(2, $estadisticas['entregados']);
        $this->assertEquals(1, $estadisticas['fallidos']);
        $this->assertEquals(2, $estadisticas['pendientes']);

        $this->assertCount(6, $repartos);
    }

    public function test_capacidad_vehiculos_segun_pedidos()
    {
        // Crear productos con peso específico
        $producto1 = \App\Models\Producto::factory()->create(['peso_kg' => 10]);
        $producto2 = \App\Models\Producto::factory()->create(['peso_kg' => 5]);

        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        // Items del pedido
        \App\Models\PedidoItem::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto1->id,
            'cantidad' => 2,
            'precio_unit_l1' => 100,
        ]);

        \App\Models\PedidoItem::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto2->id,
            'cantidad' => 3,
            'precio_unit_l1' => 50,
        ]);

        // Peso total: (10kg * 2) + (5kg * 3) = 35kg
        $pesoEsperado = 35;

        $this->assertEquals($pesoEsperado, $pedido->peso_total);

        // Verificar que el vehículo puede llevar esta carga
        $vehiculoGrande = Vehiculo::factory()->create(['capacidad_kg' => 100]);
        $vehiculoPequeño = Vehiculo::factory()->create(['capacidad_kg' => 20]);

        $this->assertTrue($vehiculoGrande->capacidad_kg >= $pesoEsperado);
        $this->assertFalse($vehiculoPequeño->capacidad_kg >= $pesoEsperado);
    }

    public function test_vehiculo_no_disponible_con_multiple_repartos()
    {
        $pedido1 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido2 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        // Vehículo ya tiene un reparto programado
        Reparto::create([
            'pedido_id' => $pedido1->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        $disponibles = Vehiculo::disponibles(today())->get();

        $this->assertCount(0, $disponibles);

        // Completar el reparto
        $reparto = Reparto::where('pedido_id', $pedido1->id)->first();
        $reparto->update(['estado' => 'entregado']);

        $disponiblesAhora = Vehiculo::disponibles(today())->get();

        $this->assertCount(1, $disponiblesAhora);
        $this->assertEquals($this->vehiculo->id, $disponiblesAhora->first()->id);
    }

    public function test_eliminar_reparto_revierte_estado_pedido()
    {
        $pedido = Pedido::factory()->create([
            'cliente_id' => $this->cliente->id,
            'estado' => 'confirmado',
        ]);

        // Crear reparto
        $reparto = Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        // El pedido debe cambiar a listo_envio cuando se crea el reparto
        $pedido->update(['estado' => 'listo_envio']); // Simular el estado tras crear reparto
        
        $pedido->refresh();
        $this->assertEquals('listo_envio', $pedido->estado);

        // Eliminar reparto
        $response = $this->deleteJson("/api/v1/repartos/{$reparto->id}");

        $response->assertStatus(200);

        $pedido->refresh();
        $this->assertEquals('confirmado', $pedido->estado);
        $this->assertDatabaseMissing('repartos', ['id' => $reparto->id]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}

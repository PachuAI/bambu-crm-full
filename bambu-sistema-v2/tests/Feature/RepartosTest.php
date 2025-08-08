<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Reparto;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RepartosTest extends TestCase
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
        $this->vehiculo = Vehiculo::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_puede_listar_repartos()
    {
        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        Reparto::factory()->count(3)->create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
        ]);

        $response = $this->getJson('/api/v1/repartos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'repartos' => [
                    '*' => [
                        'id',
                        'pedido_id',
                        'vehiculo_id',
                        'fecha_programada',
                        'estado',
                        'pedido',
                        'vehiculo',
                    ],
                ],
            ]);
    }

    public function test_puede_filtrar_repartos_por_fecha()
    {
        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->addDay(),
            'estado' => 'programado',
        ]);

        $response = $this->getJson('/api/v1/repartos?fecha='.today()->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'repartos');
    }

    public function test_puede_crear_reparto()
    {
        $pedido = Pedido::factory()->create([
            'cliente_id' => $this->cliente->id,
            'estado' => 'confirmado',
        ]);

        $repartoData = [
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'hora_salida' => '08:30',
            'observaciones' => 'Entrega matutina',
        ];

        $response = $this->postJson('/api/v1/repartos', $repartoData);

        $response->assertStatus(201)
            ->assertJsonFragment(['estado' => 'programado']);

        $this->assertDatabaseHas('repartos', [
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
        ]);

        // Verificar que el estado del pedido cambió
        $pedido->refresh();
        $this->assertEquals('listo_envio', $pedido->estado);
    }

    public function test_no_puede_crear_reparto_duplicado()
    {
        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        $repartoData = [
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/v1/repartos', $repartoData);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Este pedido ya tiene un reparto asignado']);
    }

    public function test_no_puede_asignar_vehiculo_inactivo()
    {
        $vehiculoInactivo = Vehiculo::factory()->create(['activo' => false]);
        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        $repartoData = [
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $vehiculoInactivo->id,
            'fecha_programada' => today()->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/v1/repartos', $repartoData);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'El vehículo seleccionado no está activo']);
    }

    public function test_puede_cambiar_estado_a_en_ruta()
    {
        $pedido = Pedido::factory()->create([
            'cliente_id' => $this->cliente->id,
            'estado' => 'listo_envio',
        ]);

        $reparto = Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        $response = $this->patchJson("/api/v1/repartos/{$reparto->id}/estado", [
            'estado' => 'en_ruta',
            'hora_salida' => '09:00',
        ]);

        $response->assertStatus(200);

        $reparto->refresh();
        $pedido->refresh();

        $this->assertEquals('en_ruta', $reparto->estado);
        $this->assertEquals('09:00', $reparto->hora_salida);
        $this->assertEquals('en_transito', $pedido->estado);
    }

    public function test_puede_cambiar_estado_a_entregado()
    {
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

        $response = $this->patchJson("/api/v1/repartos/{$reparto->id}/estado", [
            'estado' => 'entregado',
            'hora_entrega' => '10:30',
            'km_recorridos' => 25.5,
        ]);

        $response->assertStatus(200);

        $reparto->refresh();
        $pedido->refresh();

        $this->assertEquals('entregado', $reparto->estado);
        $this->assertEquals('10:30', $reparto->hora_entrega);
        $this->assertEquals(25.5, $reparto->km_recorridos);
        $this->assertEquals('entregado', $pedido->estado);
    }

    public function test_puede_marcar_como_fallido()
    {
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

        $response = $this->patchJson("/api/v1/repartos/{$reparto->id}/estado", [
            'estado' => 'fallido',
            'observaciones' => 'Cliente no estaba en domicilio',
        ]);

        $response->assertStatus(200);

        $reparto->refresh();
        $pedido->refresh();

        $this->assertEquals('fallido', $reparto->estado);
        $this->assertStringContainsString('Cliente no estaba', $reparto->observaciones);
        $this->assertEquals('fallido', $pedido->estado);
    }

    public function test_obtiene_planificacion_semanal()
    {
        $fechaLunes = Carbon::now()->startOfWeek();
        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => $fechaLunes,
            'estado' => 'programado',
        ]);

        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => $fechaLunes->copy()->addDays(2),
            'estado' => 'programado',
        ]);

        $response = $this->getJson('/api/v1/planificacion-semanal');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'planificacion',
                'vehiculos',
                'semana' => [
                    'inicio',
                    'fin',
                ],
            ]);

        $planificacion = $response->json('planificacion');
        $this->assertCount(7, $planificacion); // 7 días de la semana
    }

    public function test_obtiene_seguimiento_tiempo_real()
    {
        $pedido1 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);
        $pedido2 = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        Reparto::create([
            'pedido_id' => $pedido1->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        Reparto::create([
            'pedido_id' => $pedido2->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'en_ruta',
        ]);

        $response = $this->getJson('/api/v1/seguimiento-tiempo-real');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'repartos',
                'estadisticas' => [
                    'total_programados',
                    'en_ruta',
                    'entregados',
                    'fallidos',
                    'pendientes',
                ],
                'vehiculos_activos',
                'fecha',
            ]);

        $estadisticas = $response->json('estadisticas');
        $this->assertEquals(2, $estadisticas['total_programados']);
        $this->assertEquals(1, $estadisticas['en_ruta']);
        $this->assertEquals(1, $estadisticas['pendientes']);
    }

    public function test_puede_eliminar_reparto_no_entregado()
    {
        $pedido = Pedido::factory()->create([
            'cliente_id' => $this->cliente->id,
            'estado' => 'listo_envio',
        ]);

        $reparto = Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        $response = $this->deleteJson("/api/v1/repartos/{$reparto->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('repartos', ['id' => $reparto->id]);

        // Verificar que el estado del pedido volvió a confirmado
        $pedido->refresh();
        $this->assertEquals('confirmado', $pedido->estado);
    }

    public function test_no_puede_eliminar_reparto_entregado()
    {
        $pedido = Pedido::factory()->create([
            'cliente_id' => $this->cliente->id,
            'estado' => 'entregado',
        ]);

        $reparto = Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'entregado',
        ]);

        $response = $this->deleteJson("/api/v1/repartos/{$reparto->id}");

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'No se puede eliminar un reparto ya entregado']);

        $this->assertDatabaseHas('repartos', ['id' => $reparto->id]);
    }

    public function test_atributo_duracion_entrega()
    {
        $reparto = new Reparto([
            'hora_salida' => '09:00',
            'hora_entrega' => '10:30',
        ]);

        // El atributo debería devolver 90 minutos
        $this->assertEquals(90, $reparto->duracion_entrega);
    }

    public function test_scopes_funcionan_correctamente()
    {
        $pedido = Pedido::factory()->create(['cliente_id' => $this->cliente->id]);

        $reparto1 = Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado',
        ]);

        $reparto2 = Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $this->vehiculo->id,
            'fecha_programada' => today()->addDay(),
            'estado' => 'entregado',
        ]);

        // Test scope porEstado
        $programados = Reparto::porEstado('programado')->get();
        $this->assertCount(1, $programados);
        $this->assertEquals($reparto1->id, $programados->first()->id);

        // Test scope porFecha
        $hoy = Reparto::porFecha(today())->get();
        $this->assertCount(1, $hoy);
        $this->assertEquals($reparto1->id, $hoy->first()->id);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}

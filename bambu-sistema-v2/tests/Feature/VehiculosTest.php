<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Reparto;
use App\Models\Pedido;
use App\Models\Cliente;
use Laravel\Sanctum\Sanctum;

class VehiculosTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_puede_listar_vehiculos()
    {
        Vehiculo::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/vehiculos');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'vehiculos' => [
                        '*' => [
                            'id',
                            'patente',
                            'marca',
                            'modelo',
                            'anio',
                            'capacidad_kg',
                            'capacidad_bultos',
                            'activo',
                            'nombre_completo',
                            'estado_hoy',
                            'repartos_hoy'
                        ]
                    ]
                ]);
    }

    public function test_puede_crear_vehiculo()
    {
        $vehiculoData = [
            'patente' => 'ABC123',
            'marca' => 'Ford',
            'modelo' => 'Transit',
            'anio' => 2020,
            'capacidad_kg' => 1000.50,
            'capacidad_bultos' => 50.25,
            'activo' => true,
            'observaciones' => 'Vehículo en buen estado'
        ];

        $response = $this->postJson('/api/v1/vehiculos', $vehiculoData);

        $response->assertStatus(201)
                ->assertJsonFragment(['patente' => 'ABC123']);

        $this->assertDatabaseHas('vehiculos', [
            'patente' => 'ABC123',
            'marca' => 'Ford'
        ]);
    }

    public function test_no_puede_crear_vehiculo_con_patente_duplicada()
    {
        Vehiculo::factory()->create(['patente' => 'ABC123']);

        $vehiculoData = [
            'patente' => 'ABC123',
            'marca' => 'Chevrolet',
            'modelo' => 'Sprinter',
            'capacidad_kg' => 800,
            'capacidad_bultos' => 40
        ];

        $response = $this->postJson('/api/v1/vehiculos', $vehiculoData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['patente']);
    }

    public function test_puede_obtener_detalle_vehiculo()
    {
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->getJson("/api/v1/vehiculos/{$vehiculo->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'vehiculo' => [
                        'id',
                        'patente',
                        'marca',
                        'modelo',
                        'repartos'
                    ],
                    'estadisticas' => [
                        'total_repartos',
                        'repartos_entregados',
                        'repartos_fallidos',
                        'km_totales',
                        'repartos_mes_actual'
                    ]
                ]);
    }

    public function test_puede_actualizar_vehiculo()
    {
        $vehiculo = Vehiculo::factory()->create();

        $updateData = [
            'patente' => 'XYZ789',
            'marca' => 'Mercedes',
            'modelo' => 'Sprinter',
            'anio' => 2021,
            'capacidad_kg' => 1200,
            'capacidad_bultos' => 60,
            'activo' => false
        ];

        $response = $this->putJson("/api/v1/vehiculos/{$vehiculo->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonFragment(['patente' => 'XYZ789']);

        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'patente' => 'XYZ789',
            'marca' => 'Mercedes'
        ]);
    }

    public function test_puede_activar_vehiculo()
    {
        $vehiculo = Vehiculo::factory()->create(['activo' => false]);

        $response = $this->patchJson("/api/v1/vehiculos/{$vehiculo->id}/activar");

        $response->assertStatus(200);
        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'activo' => true
        ]);
    }

    public function test_puede_desactivar_vehiculo_sin_repartos_activos()
    {
        $vehiculo = Vehiculo::factory()->create(['activo' => true]);

        $response = $this->patchJson("/api/v1/vehiculos/{$vehiculo->id}/desactivar");

        $response->assertStatus(200);
        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'activo' => false
        ]);
    }

    public function test_no_puede_desactivar_vehiculo_con_repartos_activos()
    {
        $vehiculo = Vehiculo::factory()->create(['activo' => true]);
        $cliente = Cliente::factory()->create();
        $pedido = Pedido::factory()->create(['cliente_id' => $cliente->id]);
        
        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'programado'
        ]);

        $response = $this->patchJson("/api/v1/vehiculos/{$vehiculo->id}/desactivar");

        $response->assertStatus(422)
                ->assertJsonFragment(['message' => 'No se puede desactivar el vehículo porque tiene repartos activos']);

        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'activo' => true
        ]);
    }

    public function test_no_puede_eliminar_vehiculo_con_repartos_activos()
    {
        $vehiculo = Vehiculo::factory()->create();
        $cliente = Cliente::factory()->create();
        $pedido = Pedido::factory()->create(['cliente_id' => $cliente->id]);
        
        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $vehiculo->id,
            'fecha_programada' => today(),
            'estado' => 'en_ruta'
        ]);

        $response = $this->deleteJson("/api/v1/vehiculos/{$vehiculo->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('vehiculos', ['id' => $vehiculo->id]);
    }

    public function test_puede_obtener_vehiculos_disponibles()
    {
        $vehiculo1 = Vehiculo::factory()->create(['activo' => true]);
        $vehiculo2 = Vehiculo::factory()->create(['activo' => true]);
        $vehiculo3 = Vehiculo::factory()->create(['activo' => false]); // Inactivo
        
        // Vehiculo2 tiene reparto hoy
        $cliente = Cliente::factory()->create();
        $pedido = Pedido::factory()->create(['cliente_id' => $cliente->id]);
        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $vehiculo2->id,
            'fecha_programada' => today()->format('Y-m-d'),
            'estado' => 'programado'
        ]);

        $response = $this->getJson('/api/v1/vehiculos-disponibles');

        $response->assertStatus(200);
        
        // Verificar que solo devuelve vehículos activos sin repartos
        $vehiculosDisponibles = $response->json('vehiculos');
        
        // Debug: Ver qué vehículos están devolviendo
        $ids = collect($vehiculosDisponibles)->pluck('id')->toArray();
        
        // Solo vehiculo1 debe estar disponible
        $this->assertCount(1, $vehiculosDisponibles, 
            'Se esperaba 1 vehículo disponible, pero se encontraron: ' . count($vehiculosDisponibles) . 
            ' (IDs: ' . implode(', ', $ids) . '). Solo ID ' . $vehiculo1->id . ' debería estar disponible.');
        $this->assertEquals($vehiculo1->id, $vehiculosDisponibles[0]['id']);
    }

    public function test_scope_disponibles_funciona_correctamente()
    {
        $vehiculoDisponible = Vehiculo::factory()->create(['activo' => true]);
        $vehiculoOcupado = Vehiculo::factory()->create(['activo' => true]);
        $vehiculoInactivo = Vehiculo::factory()->create(['activo' => false]);

        $cliente = Cliente::factory()->create();
        $pedido = Pedido::factory()->create(['cliente_id' => $cliente->id]);
        Reparto::create([
            'pedido_id' => $pedido->id,
            'vehiculo_id' => $vehiculoOcupado->id,
            'fecha_programada' => today(),
            'estado' => 'en_ruta'
        ]);

        $disponibles = Vehiculo::disponibles(today())->get();

        $this->assertCount(1, $disponibles);
        $this->assertEquals($vehiculoDisponible->id, $disponibles->first()->id);
    }

    public function test_atributo_nombre_completo()
    {
        $vehiculo = Vehiculo::factory()->create([
            'marca' => 'Ford',
            'modelo' => 'Transit',
            'patente' => 'ABC123'
        ]);

        $this->assertEquals('Ford Transit (ABC123)', $vehiculo->nombre_completo);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
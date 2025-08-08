<?php

namespace Tests\Feature\Models;

use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Provincia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClienteModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'ProvinciasSeeder']);
        $this->artisan('db:seed', ['--class' => 'CiudadesSeeder']);
    }

    public function test_puede_crear_cliente_basico(): void
    {
        $ciudad = Ciudad::first();

        $cliente = Cliente::create([
            'nombre' => 'Juan Pérez',
            'direccion' => 'San Martín 123',
            'telefono' => '261-123-4567',
            'email' => 'juan@example.com',
            'ciudad_id' => $ciudad->id,
        ]);

        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Juan Pérez',
            'telefono' => '261-123-4567',
            'email' => 'juan@example.com',
        ]);

        $this->assertEquals('Juan Pérez', $cliente->nombre);
        $this->assertEquals('261-123-4567', $cliente->telefono);
    }

    public function test_cliente_requiere_campos_obligatorios(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Cliente::create([
            // Falta nombre, direccion, telefono, ciudad_id
            'email' => 'test@example.com',
        ]);
    }

    public function test_relacion_con_ciudad(): void
    {
        $provincia = Provincia::where('codigo', 'MZ')->first();
        $ciudad = $provincia->ciudades()->first();

        $cliente = Cliente::create([
            'nombre' => 'Test Cliente',
            'direccion' => 'Test 123',
            'telefono' => '123456789',
            'ciudad_id' => $ciudad->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $cliente->ciudad());
        $this->assertEquals($ciudad->id, $cliente->ciudad->id);
        $this->assertEquals($ciudad->nombre, $cliente->ciudad->nombre);
    }

    public function test_relacion_con_pedidos(): void
    {
        $ciudad = Ciudad::first();

        $cliente = Cliente::create([
            'nombre' => 'Test Pedidos',
            'direccion' => 'Test 123',
            'telefono' => '123456789',
            'ciudad_id' => $ciudad->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $cliente->pedidos());
        $this->assertCount(0, $cliente->pedidos);
    }

    public function test_scope_por_ciudad(): void
    {
        $ciudad1 = Ciudad::first();
        $ciudad2 = Ciudad::skip(1)->first();

        $cliente1 = Cliente::create([
            'nombre' => 'Cliente Ciudad 1',
            'direccion' => 'Test 123',
            'telefono' => '123456789',
            'ciudad_id' => $ciudad1->id,
        ]);

        $cliente2 = Cliente::create([
            'nombre' => 'Cliente Ciudad 2',
            'direccion' => 'Test 456',
            'telefono' => '987654321',
            'ciudad_id' => $ciudad2->id,
        ]);

        $clientesCiudad1 = Cliente::porCiudad($ciudad1->id)->get();
        $this->assertCount(1, $clientesCiudad1);
        $this->assertTrue($clientesCiudad1->contains($cliente1));
        $this->assertFalse($clientesCiudad1->contains($cliente2));
    }

    public function test_scope_buscar(): void
    {
        $ciudad = Ciudad::first();

        Cliente::create([
            'nombre' => 'Juan Carlos Pérez',
            'direccion' => 'Test 123',
            'telefono' => '261-123-4567',
            'email' => 'juan.perez@email.com',
            'ciudad_id' => $ciudad->id,
        ]);

        Cliente::create([
            'nombre' => 'María González',
            'direccion' => 'Test 456',
            'telefono' => '261-987-6543',
            'email' => 'maria.gonzalez@email.com',
            'ciudad_id' => $ciudad->id,
        ]);

        // Buscar por nombre
        $resultados = Cliente::buscar('Juan')->get();
        $this->assertCount(1, $resultados);

        // Buscar por teléfono
        $resultados = Cliente::buscar('261-987')->get();
        $this->assertCount(1, $resultados);

        // Buscar por email
        $resultados = Cliente::buscar('maria.gonzalez')->get();
        $this->assertCount(1, $resultados);

        // Buscar término que no coincide
        $resultados = Cliente::buscar('NoExiste')->get();
        $this->assertCount(0, $resultados);
    }

    public function test_accessor_direccion_completa(): void
    {
        $provincia = Provincia::where('codigo', 'MZ')->first();
        $ciudad = $provincia->ciudades()->first();

        $cliente = Cliente::create([
            'nombre' => 'Test Direccion',
            'direccion' => 'San Martín 123',
            'telefono' => '123456789',
            'ciudad_id' => $ciudad->id,
        ]);

        $direccionEsperada = 'San Martín 123, '.$ciudad->nombre.', '.$provincia->nombre;
        $this->assertEquals($direccionEsperada, $cliente->direccion_completa);
    }

    public function test_soft_deletes_funcionan(): void
    {
        $ciudad = Ciudad::first();

        $cliente = Cliente::create([
            'nombre' => 'Test Delete',
            'direccion' => 'Test 123',
            'telefono' => '123456789',
            'ciudad_id' => $ciudad->id,
        ]);

        $clienteId = $cliente->id;

        // Soft delete
        $cliente->delete();

        // No debe aparecer en consultas normales
        $this->assertCount(0, Cliente::where('id', $clienteId)->get());

        // Debe aparecer con withTrashed
        $this->assertCount(1, Cliente::withTrashed()->where('id', $clienteId)->get());

        // Debe estar marcado como eliminado
        $clienteEliminado = Cliente::withTrashed()->find($clienteId);
        $this->assertNotNull($clienteEliminado->deleted_at);
    }

    public function test_cliente_puede_ser_restaurado(): void
    {
        $ciudad = Ciudad::first();

        $cliente = Cliente::create([
            'nombre' => 'Test Restore',
            'direccion' => 'Test 123',
            'telefono' => '123456789',
            'ciudad_id' => $ciudad->id,
        ]);

        $clienteId = $cliente->id;

        // Eliminar y restaurar
        $cliente->delete();
        $cliente->restore();

        // Debe aparecer en consultas normales después del restore
        $this->assertCount(1, Cliente::where('id', $clienteId)->get());

        $clienteRestaurado = Cliente::find($clienteId);
        $this->assertNull($clienteRestaurado->deleted_at);
    }

    public function test_validacion_foreign_key_ciudad(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Cliente::create([
            'nombre' => 'Test FK',
            'direccion' => 'Test 123',
            'telefono' => '123456789',
            'ciudad_id' => 99999, // Ciudad inexistente
        ]);
    }
}

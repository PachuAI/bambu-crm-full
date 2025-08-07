<?php

namespace Database\Factories;

use App\Models\Ciudad;
use App\Models\Provincia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ciudades del Alto Valle de Río Negro y Neuquén
        $ciudadesAltoValle = [
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Ingeniero Huergo', 'cp' => '8334'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'General Roca', 'cp' => '8332'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Allen', 'cp' => '8328'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Cipolletti', 'cp' => '8324'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Cinco Saltos', 'cp' => '8303'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Villa Regina', 'cp' => '8336'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Chichinales', 'cp' => '8326'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Cervantes', 'cp' => '8326'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Mainqué', 'cp' => '8326'],
            ['provincia' => 'Río Negro', 'codigo' => 'RN', 'ciudad' => 'Catriel', 'cp' => '8307'],
            ['provincia' => 'Neuquén', 'codigo' => 'NQ', 'ciudad' => 'Neuquén', 'cp' => '8300'],
            ['provincia' => 'Neuquén', 'codigo' => 'NQ', 'ciudad' => 'Plottier', 'cp' => '8316'],
            ['provincia' => 'Neuquén', 'codigo' => 'NQ', 'ciudad' => 'Centenario', 'cp' => '8309'],
        ];
        
        // Seleccionar una ciudad aleatoria
        $ciudadData = $this->faker->randomElement($ciudadesAltoValle);
        
        // Crear o usar provincia existente
        $provincia = Provincia::firstOrCreate(
            ['nombre' => $ciudadData['provincia']],
            ['codigo' => $ciudadData['codigo']]
        );
        
        // Crear o usar ciudad existente
        $ciudad = Ciudad::firstOrCreate(
            [
                'nombre' => $ciudadData['ciudad'],
                'provincia_id' => $provincia->id
            ],
            ['codigo_postal' => $ciudadData['cp']]
        );

        return [
            'nombre' => $this->faker->company(),
            'direccion' => $this->faker->streetAddress(),
            'telefono' => $this->faker->numerify('299-#######'), // Teléfonos típicos de la región
            'email' => $this->faker->unique()->safeEmail(),
            'ciudad_id' => $ciudad->id,
        ];
    }
}

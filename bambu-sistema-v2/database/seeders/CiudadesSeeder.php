<?php

namespace Database\Seeders;

use App\Models\Provincia;
use App\Models\Ciudad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CiudadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mendoza = Provincia::where('codigo', 'MZ')->first();
        $buenosAires = Provincia::where('codigo', 'BA')->first();

        if ($mendoza) {
            $ciudadesMendoza = [
                ['nombre' => 'Mendoza Capital', 'codigo_postal' => '5500', 'latitud' => -32.8895, 'longitud' => -68.8458],
                ['nombre' => 'Godoy Cruz', 'codigo_postal' => '5501', 'latitud' => -32.9258, 'longitud' => -68.8700],
                ['nombre' => 'Guaymallén', 'codigo_postal' => '5521', 'latitud' => -32.8833, 'longitud' => -68.8167],
                ['nombre' => 'Las Heras', 'codigo_postal' => '5539', 'latitud' => -32.8500, 'longitud' => -68.8333],
                ['nombre' => 'Luján de Cuyo', 'codigo_postal' => '5507', 'latitud' => -33.0333, 'longitud' => -68.8667],
                ['nombre' => 'Maipú', 'codigo_postal' => '5515', 'latitud' => -32.9833, 'longitud' => -68.7833],
                ['nombre' => 'San Rafael', 'codigo_postal' => '5600', 'latitud' => -34.6177, 'longitud' => -68.3307],
                ['nombre' => 'San Martín', 'codigo_postal' => '5570', 'latitud' => -33.0833, 'longitud' => -68.4667],
            ];

            foreach ($ciudadesMendoza as $ciudad) {
                Ciudad::updateOrCreate(
                    ['nombre' => $ciudad['nombre'], 'provincia_id' => $mendoza->id],
                    array_merge($ciudad, ['provincia_id' => $mendoza->id])
                );
            }
        }

        if ($buenosAires) {
            $ciudadesBuenosAires = [
                ['nombre' => 'Buenos Aires Capital', 'codigo_postal' => '1000', 'latitud' => -34.6118, 'longitud' => -58.3960],
                ['nombre' => 'La Plata', 'codigo_postal' => '1900', 'latitud' => -34.9205, 'longitud' => -57.9536],
            ];

            foreach ($ciudadesBuenosAires as $ciudad) {
                Ciudad::updateOrCreate(
                    ['nombre' => $ciudad['nombre'], 'provincia_id' => $buenosAires->id],
                    array_merge($ciudad, ['provincia_id' => $buenosAires->id])
                );
            }
        }
    }
}

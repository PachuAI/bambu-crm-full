<?php

namespace Database\Seeders;

use App\Models\Provincia;
use Illuminate\Database\Seeder;

class ProvinciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provincias = [
            ['nombre' => 'Mendoza', 'codigo' => 'MZ'],
            ['nombre' => 'Buenos Aires', 'codigo' => 'BA'],
            ['nombre' => 'Córdoba', 'codigo' => 'CB'],
            ['nombre' => 'Santa Fe', 'codigo' => 'SF'],
            ['nombre' => 'San Luis', 'codigo' => 'SL'],
            ['nombre' => 'Neuquén', 'codigo' => 'NQ'],
            ['nombre' => 'Río Negro', 'codigo' => 'RN'],
            ['nombre' => 'La Pampa', 'codigo' => 'LP'],
        ];

        foreach ($provincias as $provincia) {
            Provincia::updateOrCreate(
                ['codigo' => $provincia['codigo']],
                $provincia
            );
        }
    }
}

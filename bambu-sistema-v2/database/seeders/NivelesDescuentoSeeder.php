<?php

namespace Database\Seeders;

use App\Models\NivelDescuento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NivelesDescuentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveles = [
            [
                'nombre' => 'L1 - Precio Lista',
                'porcentaje_descuento' => 0.00,
                'monto_minimo' => null,
                'descripcion' => 'Precio de lista sin descuento',
                'activo' => true,
            ],
            [
                'nombre' => 'L2 - Descuento Comercial',
                'porcentaje_descuento' => 10.00,
                'monto_minimo' => 50000.00,
                'descripcion' => 'Descuento para compras comerciales mayores a $50.000',
                'activo' => true,
            ],
            [
                'nombre' => 'L3 - Mayorista',
                'porcentaje_descuento' => 15.00,
                'monto_minimo' => 100000.00,
                'descripcion' => 'Descuento mayorista para compras mayores a $100.000',
                'activo' => true,
            ],
            [
                'nombre' => 'L4 - Distribuidor',
                'porcentaje_descuento' => 20.00,
                'monto_minimo' => 200000.00,
                'descripcion' => 'Descuento para distribuidores con compras mayores a $200.000',
                'activo' => true,
            ],
            [
                'nombre' => 'Promoción Especial',
                'porcentaje_descuento' => 25.00,
                'monto_minimo' => 300000.00,
                'descripcion' => 'Promoción especial temporaria',
                'activo' => false,
            ],
        ];

        foreach ($niveles as $nivel) {
            NivelDescuento::updateOrCreate(
                ['nombre' => $nivel['nombre']],
                $nivel
            );
        }
    }
}

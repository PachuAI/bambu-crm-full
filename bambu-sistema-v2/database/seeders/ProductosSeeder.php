<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            // Productos BAMBU
            [
                'nombre' => 'BAMBU Pasta Dental Menta 150ml',
                'sku' => 'BAMBU-PD-MENTA-150',
                'precio_base_l1' => 2500.00,
                'stock_actual' => 100,
                'es_combo' => false,
                'marca_producto' => 'BAMBU',
                'descripcion' => 'Pasta dental con extractos naturales de menta, sin flúor',
                'peso_kg' => 0.200,
            ],
            [
                'nombre' => 'BAMBU Shampoo Natural 300ml',
                'sku' => 'BAMBU-SH-NAT-300',
                'precio_base_l1' => 3200.00,
                'stock_actual' => 75,
                'es_combo' => false,
                'marca_producto' => 'BAMBU',
                'descripcion' => 'Shampoo con ingredientes naturales, sin sulfatos',
                'peso_kg' => 0.350,
            ],
            [
                'nombre' => 'BAMBU Jabón Artesanal Lavanda 100g',
                'sku' => 'BAMBU-JA-LAV-100',
                'precio_base_l1' => 1800.00,
                'stock_actual' => 200,
                'es_combo' => false,
                'marca_producto' => 'BAMBU',
                'descripcion' => 'Jabón artesanal con aceites esenciales de lavanda',
                'peso_kg' => 0.120,
            ],
            [
                'nombre' => 'BAMBU Crema Hidratante 250ml',
                'sku' => 'BAMBU-CH-250',
                'precio_base_l1' => 4500.00,
                'stock_actual' => 50,
                'es_combo' => false,
                'marca_producto' => 'BAMBU',
                'descripcion' => 'Crema hidratante con aloe vera y extractos naturales',
                'peso_kg' => 0.300,
            ],

            // Productos SAPHIRUS
            [
                'nombre' => 'SAPHIRUS Perfume Masculino 100ml',
                'sku' => 'SAPH-PM-100',
                'precio_base_l1' => 8500.00,
                'stock_actual' => 30,
                'es_combo' => false,
                'marca_producto' => 'SAPHIRUS',
                'descripcion' => 'Perfume masculino de larga duración',
                'peso_kg' => 0.250,
            ],
            [
                'nombre' => 'SAPHIRUS Perfume Femenino 100ml',
                'sku' => 'SAPH-PF-100',
                'precio_base_l1' => 8500.00,
                'stock_actual' => 25,
                'es_combo' => false,
                'marca_producto' => 'SAPHIRUS',
                'descripcion' => 'Perfume femenino floral y elegante',
                'peso_kg' => 0.250,
            ],

            // Combos
            [
                'nombre' => 'COMBO BAMBU Higiene Personal',
                'sku' => 'COMBO-BAMBU-HP-01',
                'precio_base_l1' => 6500.00,
                'stock_actual' => 20,
                'es_combo' => true,
                'marca_producto' => 'BAMBU',
                'descripcion' => 'Combo: Pasta dental + Shampoo + Jabón artesanal',
                'peso_kg' => 0.670,
            ],
            [
                'nombre' => 'COMBO BAMBU Cuidado de Piel',
                'sku' => 'COMBO-BAMBU-CP-01',
                'precio_base_l1' => 5800.00,
                'stock_actual' => 15,
                'es_combo' => true,
                'marca_producto' => 'BAMBU',
                'descripcion' => 'Combo: Crema hidratante + 2 Jabones artesanales',
                'peso_kg' => 0.540,
            ],

            // Productos adicionales
            [
                'nombre' => 'BAMBU Aceite Corporal Coco 200ml',
                'sku' => 'BAMBU-AC-COCO-200',
                'precio_base_l1' => 3800.00,
                'stock_actual' => 40,
                'es_combo' => false,
                'marca_producto' => 'BAMBU',
                'descripcion' => 'Aceite corporal natural de coco virgen',
                'peso_kg' => 0.220,
            ],
            [
                'nombre' => 'BAMBU Bálsamo Labial Natural 15ml',
                'sku' => 'BAMBU-BL-NAT-15',
                'precio_base_l1' => 1200.00,
                'stock_actual' => 150,
                'es_combo' => false,
                'marca_producto' => 'BAMBU',
                'descripcion' => 'Bálsamo labial con cera de abeja y vitamina E',
                'peso_kg' => 0.020,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(
                ['sku' => $producto['sku']],
                $producto
            );
        }
    }
}

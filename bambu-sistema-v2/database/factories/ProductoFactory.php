<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productos = [
            'Limpiador Amoniacal 5L',
            'Desinfectante Clorado 5L', 
            'Desengrasante Industrial 5L',
            'Detergente Lavavajillas 5L',
            'Cera Autobrillante 5L',
            'Combo Limpieza Básica',
            'Combo Desinfección Total',
        ];

        $marcas = ['BAMBU', 'ECO-CLEAN', 'INDUSTRIAL-PRO'];
        $esCombo = $this->faker->boolean(20); // 20% chance combo

        return [
            'nombre' => $this->faker->randomElement($productos),
            'sku' => strtoupper($this->faker->lexify('???')) . $this->faker->numberBetween(100, 999),
            'precio_base_l1' => $this->faker->randomFloat(2, 850, 3500),
            'stock_actual' => $this->faker->numberBetween(0, 100),
            'stock_minimo' => $this->faker->numberBetween(5, 20),
            'fabricar_siguiente' => $this->faker->boolean(10),
            'es_combo' => $esCombo,
            'marca_producto' => $this->faker->randomElement($marcas),
            'descripcion' => $this->faker->sentence(10),
            'peso_kg' => $esCombo ? $this->faker->randomFloat(3, 10, 30) : $this->faker->randomFloat(3, 4.5, 6),
        ];
    }
}

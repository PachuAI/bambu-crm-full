<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vehiculo;

class VehiculoFactory extends Factory
{
    protected $model = Vehiculo::class;

    public function definition(): array
    {
        $marcas = ['Ford', 'Chevrolet', 'Mercedes', 'Iveco', 'Volkswagen', 'Renault'];
        $modelos = ['Transit', 'Sprinter', 'Daily', 'Ducato', 'Master', 'Boxer'];

        return [
            'patente' => strtoupper($this->faker->bothify('???###')),
            'marca' => $this->faker->randomElement($marcas),
            'modelo' => $this->faker->randomElement($modelos),
            'anio' => $this->faker->numberBetween(2015, 2024),
            'capacidad_kg' => $this->faker->numberBetween(800, 3500),
            'capacidad_bultos' => $this->faker->numberBetween(30, 150),
            'activo' => true,
            'observaciones' => $this->faker->optional()->sentence(),
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }

    public function conPatente(string $patente): static
    {
        return $this->state(fn (array $attributes) => [
            'patente' => $patente,
        ]);
    }

    public function conCapacidad(float $kg, float $bultos): static
    {
        return $this->state(fn (array $attributes) => [
            'capacidad_kg' => $kg,
            'capacidad_bultos' => $bultos,
        ]);
    }
}
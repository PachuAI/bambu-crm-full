<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        $estados = ['borrador', 'confirmado', 'listo_envio', 'en_transito', 'entregado', 'fallido', 'cancelado'];
        $montoBruto = $this->faker->randomFloat(2, 1000, 50000);
        $descuento = $this->faker->randomFloat(2, 0, $montoBruto * 0.2); // Hasta 20% descuento

        return [
            'cliente_id' => Cliente::factory(),
            'nivel_descuento_id' => null,
            'monto_bruto' => $montoBruto,
            'monto_final' => $montoBruto - $descuento,
            'estado' => $this->faker->randomElement($estados),
            'fecha_reparto' => $this->faker->optional(0.7)->dateTimeBetween('-30 days', '+30 days'),
        ];
    }

    public function confirmado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'confirmado',
        ]);
    }

    public function listoEnvio(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'listo_envio',
        ]);
    }

    public function enTransito(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'en_transito',
        ]);
    }

    public function entregado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'entregado',
        ]);
    }

    public function fallido(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'fallido',
        ]);
    }

    public function conCliente($clienteId): static
    {
        return $this->state(fn (array $attributes) => [
            'cliente_id' => $clienteId,
        ]);
    }

    public function conMonto($monto): static
    {
        return $this->state(fn (array $attributes) => [
            'monto_bruto' => $monto,
            'monto_final' => $monto,
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Reparto;
use App\Models\Pedido;
use App\Models\Vehiculo;

class RepartoFactory extends Factory
{
    protected $model = Reparto::class;

    public function definition(): array
    {
        $estados = ['programado', 'en_ruta', 'entregado', 'fallido'];
        $fechaProgramada = $this->faker->dateTimeBetween('-7 days', '+7 days');

        return [
            'pedido_id' => Pedido::factory(),
            'vehiculo_id' => Vehiculo::factory(),
            'fecha_programada' => $fechaProgramada->format('Y-m-d'),
            'hora_salida' => $this->faker->optional(0.7)->time('H:i'),
            'hora_entrega' => $this->faker->optional(0.5)->time('H:i'),
            'estado' => $this->faker->randomElement($estados),
            'observaciones' => $this->faker->optional(0.3)->sentence(),
            'km_recorridos' => $this->faker->optional(0.6)->randomFloat(2, 5, 50),
        ];
    }

    public function programado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'programado',
            'hora_salida' => null,
            'hora_entrega' => null,
            'km_recorridos' => null,
        ]);
    }

    public function enRuta(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'en_ruta',
            'hora_salida' => $this->faker->time('H:i'),
            'hora_entrega' => null,
            'km_recorridos' => null,
        ]);
    }

    public function entregado(): static
    {
        $horaSalida = $this->faker->time('H:i');
        $horaEntrega = $this->faker->time('H:i', $horaSalida);

        return $this->state(fn (array $attributes) => [
            'estado' => 'entregado',
            'hora_salida' => $horaSalida,
            'hora_entrega' => $horaEntrega,
            'km_recorridos' => $this->faker->randomFloat(2, 10, 40),
        ]);
    }

    public function fallido(): static
    {
        $horaSalida = $this->faker->time('H:i');

        return $this->state(fn (array $attributes) => [
            'estado' => 'fallido',
            'hora_salida' => $horaSalida,
            'hora_entrega' => $this->faker->time('H:i', $horaSalida),
            'observaciones' => $this->faker->randomElement([
                'Cliente no se encontraba en domicilio',
                'Dirección incorrecta',
                'Cliente rechazó la entrega',
                'Problemas de acceso al domicilio',
                'Horario no conveniente para el cliente'
            ]),
            'km_recorridos' => $this->faker->randomFloat(2, 5, 25),
        ]);
    }

    public function hoy(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_programada' => today()->format('Y-m-d'),
        ]);
    }

    public function ayer(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_programada' => yesterday()->format('Y-m-d'),
        ]);
    }

    public function maniana(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_programada' => tomorrow()->format('Y-m-d'),
        ]);
    }

    public function conVehiculo($vehiculoId): static
    {
        return $this->state(fn (array $attributes) => [
            'vehiculo_id' => $vehiculoId,
        ]);
    }

    public function conPedido($pedidoId): static
    {
        return $this->state(fn (array $attributes) => [
            'pedido_id' => $pedidoId,
        ]);
    }

    public function enFecha($fecha): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_programada' => is_string($fecha) ? $fecha : $fecha->format('Y-m-d'),
        ]);
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('patente', 10)->unique();
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->year('anio')->nullable();
            $table->decimal('capacidad_kg', 8, 2); // Capacidad máxima en kg
            $table->decimal('capacidad_bultos', 6, 2); // Capacidad en bultos (calculado)
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};

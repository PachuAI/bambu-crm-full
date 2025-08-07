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
        Schema::table('pedidos', function (Blueprint $table) {
            // Cambiar la columna estado para incluir nuevos estados logísticos
            $table->enum('estado', [
                'borrador', 
                'confirmado', 
                'listo_envio',    // Nuevo: Asignado a reparto
                'en_transito',    // Nuevo: Vehículo en ruta
                'entregado', 
                'fallido',        // Nuevo: Falló la entrega
                'cancelado'
            ])->default('borrador')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Revertir a los estados originales
            $table->enum('estado', [
                'borrador', 
                'confirmado', 
                'en_reparto', 
                'entregado', 
                'cancelado'
            ])->default('borrador')->change();
        });
    }
};

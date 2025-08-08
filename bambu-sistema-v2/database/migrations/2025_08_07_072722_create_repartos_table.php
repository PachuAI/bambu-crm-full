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
        Schema::create('repartos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->onDelete('set null');
            $table->date('fecha_programada');
            $table->time('hora_salida')->nullable();
            $table->time('hora_entrega')->nullable();
            $table->enum('estado', ['programado', 'en_ruta', 'entregado', 'fallido'])->default('programado');
            $table->text('observaciones')->nullable();
            $table->decimal('km_recorridos', 8, 2)->nullable();
            $table->timestamps();

            $table->index(['fecha_programada', 'estado']);
            $table->index('vehiculo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repartos');
    }
};

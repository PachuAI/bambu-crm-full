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
        Schema::create('niveles_descuento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100); // L1, L2, L3, Mayorista, etc.
            $table->decimal('porcentaje_descuento', 5, 2); // 0-100%
            $table->decimal('monto_minimo', 12, 2)->nullable(); // Compra mínima
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niveles_descuento');
    }
};

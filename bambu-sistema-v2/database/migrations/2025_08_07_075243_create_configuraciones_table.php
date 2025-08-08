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
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique(); // umbral_descuento_l2, vigencia_cotizacion, etc.
            $table->text('valor'); // JSON o valor simple
            $table->string('tipo', 50)->default('string'); // string, number, boolean, json
            $table->text('descripcion')->nullable();
            $table->string('categoria', 50)->default('general'); // general, descuentos, cotizaciones, sistema
            $table->boolean('es_publico')->default(false); // Si puede ser leído por API pública
            $table->timestamps();

            $table->index(['categoria', 'es_publico']);
            $table->index('clave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};

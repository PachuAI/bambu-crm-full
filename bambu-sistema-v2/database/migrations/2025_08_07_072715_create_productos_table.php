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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('sku', 50)->unique();
            $table->decimal('precio_base_l1', 12, 2);
            $table->unsignedInteger('stock_actual')->default(0);
            $table->boolean('es_combo')->default(false);
            $table->string('marca_producto', 100)->nullable(); // BAMBU, SAPHIRUS, etc.
            $table->text('descripcion')->nullable();
            $table->decimal('peso_kg', 8, 3)->nullable(); // Para cálculo de bultos
            $table->timestamps();
            $table->softDeletes();

            $table->index(['marca_producto', 'es_combo']);
            $table->index('stock_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

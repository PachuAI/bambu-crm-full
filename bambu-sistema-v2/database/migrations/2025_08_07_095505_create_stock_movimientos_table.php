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
        Schema::create('stock_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->enum('tipo', ['ingreso', 'venta', 'ajuste_positivo', 'ajuste_negativo', 'produccion']);
            $table->decimal('cantidad', 10, 3);
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->text('motivo')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos');
            $table->string('lote_produccion')->nullable();
            $table->timestamps();
            
            // Índices para consultas frecuentes
            $table->index(['producto_id', 'created_at']);
            $table->index(['tipo', 'created_at']);
            $table->index('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movimientos');
    }
};

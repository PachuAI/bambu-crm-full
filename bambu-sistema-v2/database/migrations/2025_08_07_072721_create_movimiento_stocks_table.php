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
        Schema::create('movimiento_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('set null');
            $table->enum('tipo', ['entrada', 'salida']); // Entrada = stock in, Salida = venta/pedido
            $table->integer('cantidad'); // Puede ser negativo para salidas
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->text('motivo')->nullable(); // Razón del movimiento
            $table->timestamps();
            
            $table->index(['producto_id', 'tipo']);
            $table->index('pedido_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_stocks');
    }
};

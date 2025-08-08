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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('accion', 100); // create, update, delete, login, etc.
            $table->string('modelo', 100)->nullable(); // Cliente, Producto, Pedido, etc.
            $table->unsignedBigInteger('modelo_id')->nullable(); // ID del registro afectado
            $table->json('datos_anteriores')->nullable(); // Estado anterior (para updates/deletes)
            $table->json('datos_nuevos')->nullable(); // Estado nuevo (para creates/updates)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['modelo', 'modelo_id']);
            $table->index(['user_id', 'accion']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};

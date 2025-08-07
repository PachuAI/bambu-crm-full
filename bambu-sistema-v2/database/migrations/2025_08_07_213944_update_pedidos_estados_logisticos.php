<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            // Para PostgreSQL necesitamos usar SQL raw para modificar enums
            DB::statement("
                ALTER TABLE pedidos 
                DROP CONSTRAINT IF EXISTS pedidos_estado_check
            ");
            
            DB::statement("
                ALTER TABLE pedidos 
                ADD CONSTRAINT pedidos_estado_check 
                CHECK (estado IN ('borrador', 'confirmado', 'listo_envio', 'en_transito', 'entregado', 'fallido', 'cancelado'))
            ");
        } else {
            // Para SQLite y otras bases, usar Schema Builder (solo funciona si no hay datos conflictivos)
            Schema::table('pedidos', function (Blueprint $table) {
                // SQLite es más flexible con strings, solo necesitamos que los nuevos valores sean aceptados
                $table->string('estado')->default('borrador')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE pedidos 
                DROP CONSTRAINT IF EXISTS pedidos_estado_check
            ");
            
            DB::statement("
                ALTER TABLE pedidos 
                ADD CONSTRAINT pedidos_estado_check 
                CHECK (estado IN ('borrador', 'confirmado', 'en_reparto', 'entregado', 'cancelado'))
            ");
        }
        // Para SQLite no necesitamos hacer nada en el down ya que acepta cualquier string
    }
};

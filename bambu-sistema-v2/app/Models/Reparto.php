<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reparto extends Model
{
    use HasFactory;
    
    protected $table = 'repartos';

    protected $fillable = [
        'pedido_id',
        'vehiculo_id',
        'fecha_programada',
        'hora_salida',
        'hora_entrega',
        'estado',
        'observaciones',
        'km_recorridos',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'hora_salida' => 'datetime:H:i',
        'hora_entrega' => 'datetime:H:i',
        'km_recorridos' => 'decimal:2',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha_programada', $fecha);
    }

    public function scopeEnRango($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_programada', [$desde, $hasta]);
    }

    public function getDuracionEntregaAttribute()
    {
        if ($this->hora_salida && $this->hora_entrega) {
            return $this->hora_entrega->diffInMinutes($this->hora_salida);
        }
        return null;
    }
}

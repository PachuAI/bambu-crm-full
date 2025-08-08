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

    // Accessors para formateo de horas
    public function getHoraSalidaAttribute($value)
    {
        if ($value) {
            return \Carbon\Carbon::createFromFormat('H:i:s', $value)->format('H:i');
        }
        return $value;
    }

    public function getHoraEntregaAttribute($value)
    {
        if ($value) {
            return \Carbon\Carbon::createFromFormat('H:i:s', $value)->format('H:i');
        }
        return $value;
    }

    // Mutators para almacenar horas
    public function setHoraSalidaAttribute($value)
    {
        if ($value && is_string($value)) {
            $this->attributes['hora_salida'] = $value . ':00';
        }
    }

    public function setHoraEntregaAttribute($value)
    {
        if ($value && is_string($value)) {
            $this->attributes['hora_entrega'] = $value . ':00';
        }
    }

    public function getDuracionEntregaAttribute()
    {
        if ($this->attributes['hora_salida'] && $this->attributes['hora_entrega']) {
            $salida = \Carbon\Carbon::createFromFormat('H:i:s', $this->attributes['hora_salida']);
            $entrega = \Carbon\Carbon::createFromFormat('H:i:s', $this->attributes['hora_entrega']);
            return $salida->diffInMinutes($entrega);
        }

        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'patente',
        'marca',
        'modelo',
        'anio',
        'capacidad_kg',
        'capacidad_bultos',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'capacidad_kg' => 'decimal:2',
        'capacidad_bultos' => 'decimal:2',
        'activo' => 'boolean',
        'anio' => 'integer',
    ];

    public function repartos(): HasMany
    {
        return $this->hasMany(Reparto::class, 'vehiculo_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDisponibles($query, $fecha)
    {
        return $query->activos()
            ->whereDoesntHave('repartos', function ($query) use ($fecha) {
                $query->whereDate('fecha_programada', $fecha)
                    ->whereIn('estado', ['programado', 'en_ruta']);
            });
    }

    public function getNombreCompletoAttribute()
    {
        return $this->marca.' '.$this->modelo.' ('.$this->patente.')';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ciudad extends Model
{
    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'provincia_id',
        'codigo_postal',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'ciudad_id');
    }

    public function scopePorProvincia($query, $provinciaId)
    {
        return $query->where('provincia_id', $provinciaId);
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ', ' . $this->provincia->nombre;
    }
}

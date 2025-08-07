<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NivelDescuento extends Model
{
    protected $table = 'niveles_descuento';

    protected $fillable = [
        'nombre',
        'porcentaje_descuento',
        'monto_minimo',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'porcentaje_descuento' => 'decimal:2',
        'monto_minimo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'nivel_descuento_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function calcularDescuento($monto)
    {
        if (!$this->activo) {
            return 0;
        }

        if ($this->monto_minimo && $monto < $this->monto_minimo) {
            return 0;
        }

        return $monto * ($this->porcentaje_descuento / 100);
    }
}

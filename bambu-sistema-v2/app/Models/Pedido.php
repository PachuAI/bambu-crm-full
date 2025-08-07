<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_id',
        'nivel_descuento_id',
        'monto_bruto',
        'monto_final',
        'estado',
        'fecha_reparto',
    ];

    protected $casts = [
        'monto_bruto' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'fecha_reparto' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function nivelDescuento(): BelongsTo
    {
        return $this->belongsTo(NivelDescuento::class, 'nivel_descuento_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }

    public function reparto(): HasOne
    {
        return $this->hasOne(Reparto::class, 'pedido_id');
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    public function getDescuentoAplicadoAttribute()
    {
        return $this->monto_bruto - $this->monto_final;
    }

    public function getPesoTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return ($item->producto->peso_kg ?? 0) * $item->cantidad;
        });
    }
}

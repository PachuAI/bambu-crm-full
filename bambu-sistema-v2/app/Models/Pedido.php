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
    
    // Estados válidos del enum (actualizados según migración update_pedidos_estados_logisticos)
    public const ESTADOS_VALIDOS = ['borrador', 'confirmado', 'listo_envio', 'en_transito', 'entregado', 'fallido', 'cancelado'];

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

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($pedido) {
            if (!in_array($pedido->estado, self::ESTADOS_VALIDOS)) {
                throw new \InvalidArgumentException("Estado '{$pedido->estado}' no es válido. Estados válidos: " . implode(', ', self::ESTADOS_VALIDOS));
            }
        });
        
        static::updating(function ($pedido) {
            if ($pedido->isDirty('estado') && !in_array($pedido->estado, self::ESTADOS_VALIDOS)) {
                throw new \InvalidArgumentException("Estado '{$pedido->estado}' no es válido. Estados válidos: " . implode(', ', self::ESTADOS_VALIDOS));
            }
        });
    }

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

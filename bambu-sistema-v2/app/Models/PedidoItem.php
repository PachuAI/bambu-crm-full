<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItem extends Model
{
    protected $table = 'pedido_items';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unit_l1',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unit_l1' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function getPesoTotalAttribute()
    {
        return ($this->producto->peso_kg ?? 0) * $this->cantidad;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($pedidoItem) {
            $pedidoItem->subtotal = $pedidoItem->cantidad * $pedidoItem->precio_unit_l1;
        });
    }
}

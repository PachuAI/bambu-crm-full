<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'sku',
        'precio_base_l1',
        'stock_actual',
        'es_combo',
        'marca_producto',
        'descripcion',
        'peso_kg',
    ];

    protected $casts = [
        'precio_base_l1' => 'decimal:2',
        'peso_kg' => 'decimal:3',
        'es_combo' => 'boolean',
        'stock_actual' => 'integer',
    ];

    public function pedidoItems(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'producto_id');
    }

    public function movimientosStock(): HasMany
    {
        return $this->hasMany(MovimientoStock::class, 'producto_id');
    }

    public function scopeEnStock($query)
    {
        return $query->where('stock_actual', '>', 0);
    }

    public function scopePorMarca($query, $marca)
    {
        return $query->where('marca_producto', $marca);
    }

    public function scopeCombo($query)
    {
        return $query->where('es_combo', true);
    }

    public function getPesoTotalAttribute()
    {
        return $this->peso_kg * $this->stock_actual;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'sku',
        'precio_base_l1',
        'stock_actual',
        'stock_minimo',
        'fabricar_siguiente',
        'es_combo',
        'marca_producto',
        'descripcion',
        'peso_kg',
    ];

    protected $casts = [
        'precio_base_l1' => 'decimal:2',
        'peso_kg' => 'decimal:3',
        'es_combo' => 'boolean',
        'fabricar_siguiente' => 'boolean',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
    ];

    public function pedidoItems(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'producto_id');
    }

    public function stockMovimientos(): HasMany
    {
        return $this->hasMany(StockMovimiento::class, 'producto_id');
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

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<=', 'stock_minimo');
    }

    public function scopeParaFabricar($query)
    {
        return $query->where('fabricar_siguiente', true)
            ->orWhereColumn('stock_actual', '<=', 'stock_minimo');
    }

    public function getPesoTotalAttribute()
    {
        return $this->peso_kg * $this->stock_actual;
    }

    public function getEstadoStockAttribute()
    {
        if ($this->stock_actual <= 0) {
            return 'sin_stock';
        }

        if ($this->stock_actual <= $this->stock_minimo) {
            return 'stock_bajo';
        }

        return 'stock_ok';
    }

    public function getDiasStockRestanteAttribute()
    {
        // Cálculo aproximado basado en ventas de los últimos 30 días
        $ventasRecientes = $this->stockMovimientos()
            ->porTipo('venta')
            ->entreFechas(now()->subDays(30), now())
            ->sum('cantidad');

        if ($ventasRecientes > 0) {
            $promedioVentaDiaria = $ventasRecientes / 30;

            return $this->stock_actual > 0 ? round($this->stock_actual / $promedioVentaDiaria) : 0;
        }

        return null;
    }
}

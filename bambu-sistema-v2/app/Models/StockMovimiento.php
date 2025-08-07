<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovimiento extends Model
{
    protected $table = 'stock_movimientos';

    protected $fillable = [
        'producto_id',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'motivo',
        'usuario_id',
        'pedido_id',
        'lote_produccion',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    public function scopeAjustesNegativos($query)
    {
        return $query->where('tipo', 'ajuste_negativo');
    }

    public function getEsCriticoAttribute()
    {
        return in_array($this->tipo, ['ajuste_negativo']) && $this->cantidad > 10;
    }

    public function getTipoFormateadoAttribute()
    {
        return match ($this->tipo) {
            'ingreso' => 'Ingreso de Stock',
            'venta' => 'Venta',
            'ajuste_positivo' => 'Ajuste Positivo',
            'ajuste_negativo' => 'Ajuste Negativo',
            'produccion' => 'Producción',
            default => $this->tipo
        };
    }
}

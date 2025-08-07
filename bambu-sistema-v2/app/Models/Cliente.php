<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'ciudad_id',
    ];

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function scopePorCiudad($query, $ciudadId)
    {
        return $query->where('ciudad_id', $ciudadId);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'LIKE', "%{$termino}%")
              ->orWhere('telefono', 'LIKE', "%{$termino}%")
              ->orWhere('email', 'LIKE', "%{$termino}%");
        });
    }

    public function getDireccionCompletaAttribute()
    {
        return $this->direccion . ', ' . $this->ciudad->nombre . ', ' . $this->ciudad->provincia->nombre;
    }
}

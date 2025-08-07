<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'descripcion',
        'categoria',
        'es_publico',
    ];

    protected $casts = [
        'es_publico' => 'boolean',
    ];

    public function scopePublicas($query)
    {
        return $query->where('es_publico', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function getValorParsedAttribute()
    {
        return match ($this->tipo) {
            'boolean' => filter_var($this->valor, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($this->valor) ? (float) $this->valor : 0,
            'json' => json_decode($this->valor, true),
            default => $this->valor
        };
    }

    public static function obtener($clave, $default = null)
    {
        $config = Cache::remember("config.{$clave}", 3600, function () use ($clave) {
            return self::where('clave', $clave)->first();
        });

        return $config ? $config->valor_parsed : $default;
    }

    public static function establecer($clave, $valor, $tipo = 'string', $descripcion = null, $categoria = 'general', $esPublico = false)
    {
        $valorString = is_array($valor) || is_object($valor) ? json_encode($valor) : (string) $valor;
        
        $configuracion = self::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valorString,
                'tipo' => $tipo,
                'descripcion' => $descripcion,
                'categoria' => $categoria,
                'es_publico' => $esPublico,
            ]
        );

        Cache::forget("config.{$clave}");
        
        return $configuracion;
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($configuracion) {
            Cache::forget("config.{$configuracion->clave}");
        });

        static::deleted(function ($configuracion) {
            Cache::forget("config.{$configuracion->clave}");
        });
    }
}

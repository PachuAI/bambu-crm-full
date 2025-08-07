<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            // DESCUENTOS
            [
                'clave' => 'umbral_descuento_l2',
                'valor' => '50000.00',
                'tipo' => 'decimal',
                'descripcion' => 'Monto mínimo para aplicar descuento L2 (5%)',
                'categoria' => 'descuentos',
                'es_publico' => true,
            ],
            [
                'clave' => 'umbral_descuento_l3',
                'valor' => '150000.00',
                'tipo' => 'decimal',
                'descripcion' => 'Monto mínimo para aplicar descuento L3 (10%)',
                'categoria' => 'descuentos',
                'es_publico' => true,
            ],
            [
                'clave' => 'porcentaje_descuento_l2',
                'valor' => '5.00',
                'tipo' => 'decimal',
                'descripcion' => 'Porcentaje de descuento nivel L2',
                'categoria' => 'descuentos',
                'es_publico' => true,
            ],
            [
                'clave' => 'porcentaje_descuento_l3',
                'valor' => '10.00',
                'tipo' => 'decimal',
                'descripcion' => 'Porcentaje de descuento nivel L3',
                'categoria' => 'descuentos',
                'es_publico' => true,
            ],
            
            // COTIZACIONES
            [
                'clave' => 'vigencia_cotizacion_dias',
                'valor' => '15',
                'tipo' => 'integer',
                'descripcion' => 'Días de vigencia de una cotización',
                'categoria' => 'cotizaciones',
                'es_publico' => true,
            ],
            [
                'clave' => 'mensaje_pie_cotizacion',
                'valor' => 'Precios válidos por 15 días. Consulte disponibilidad antes de confirmar.',
                'tipo' => 'string',
                'descripcion' => 'Mensaje que aparece al pie de las cotizaciones',
                'categoria' => 'cotizaciones',
                'es_publico' => false,
            ],
            
            // LOGÍSTICA
            [
                'clave' => 'peso_maximo_bulto_kg',
                'valor' => '25.0',
                'tipo' => 'decimal',
                'descripcion' => 'Peso máximo por bulto para reparto',
                'categoria' => 'logistica',
                'es_publico' => false,
            ],
            [
                'clave' => 'zona_entrega_gratuita_km',
                'valor' => '50',
                'tipo' => 'integer',
                'descripcion' => 'Radio en km para entrega gratuita desde depósito',
                'categoria' => 'logistica',
                'es_publico' => true,
            ],
            
            // SISTEMA
            [
                'clave' => 'empresa_nombre',
                'valor' => 'BAMBU Distribuciones',
                'tipo' => 'string',
                'descripcion' => 'Nombre de la empresa',
                'categoria' => 'sistema',
                'es_publico' => true,
            ],
            [
                'clave' => 'empresa_telefono',
                'valor' => '261-1234567',
                'tipo' => 'string',
                'descripcion' => 'Teléfono principal de la empresa',
                'categoria' => 'sistema',
                'es_publico' => true,
            ],
            [
                'clave' => 'empresa_email',
                'valor' => 'info@bambu.com',
                'tipo' => 'string',
                'descripcion' => 'Email principal de la empresa',
                'categoria' => 'sistema',
                'es_publico' => true,
            ],
            [
                'clave' => 'backup_automatico_hora',
                'valor' => '02:00',
                'tipo' => 'string',
                'descripcion' => 'Hora para ejecutar backup automático (HH:MM)',
                'categoria' => 'sistema',
                'es_publico' => false,
            ],
        ];

        foreach ($configuraciones as $config) {
            DB::table('configuraciones')->updateOrInsert(
                ['clave' => $config['clave']],
                array_merge($config, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}

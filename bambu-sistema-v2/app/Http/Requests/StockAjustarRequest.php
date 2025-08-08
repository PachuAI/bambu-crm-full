<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAjustarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'nueva_cantidad' => 'required|integer|min:0',
            'motivo' => 'required|string|max:500|min:5',
            'lote_produccion' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'producto_id.required' => 'El producto es obligatorio',
            'producto_id.exists' => 'El producto seleccionado no existe',
            'nueva_cantidad.required' => 'La nueva cantidad es obligatoria',
            'nueva_cantidad.integer' => 'La cantidad debe ser un número entero',
            'nueva_cantidad.min' => 'La cantidad no puede ser negativa',
            'motivo.required' => 'El motivo del ajuste es obligatorio',
            'motivo.min' => 'El motivo debe tener al menos 5 caracteres',
            'motivo.max' => 'El motivo no puede exceder 500 caracteres',
            'lote_produccion.max' => 'El lote de producción no puede exceder 100 caracteres',
        ];
    }

    protected function prepareForValidation()
    {
        // Si es un ajuste negativo, asegurar que hay motivo
        $productoId = $this->input('producto_id');
        $nuevaCantidad = $this->input('nueva_cantidad');
        
        if ($productoId && $nuevaCantidad !== null) {
            $producto = \App\Models\Producto::find($productoId);
            if ($producto && $nuevaCantidad < $producto->stock_actual && empty($this->input('motivo'))) {
                $this->merge([
                    'motivo_requerido_por_ajuste_negativo' => true
                ]);
            }
        }
    }
}
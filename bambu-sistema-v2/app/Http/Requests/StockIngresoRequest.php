<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockIngresoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1|max:10000',
            'motivo' => 'nullable|string|max:500',
            'lote_produccion' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'producto_id.required' => 'El producto es obligatorio',
            'producto_id.exists' => 'El producto seleccionado no existe',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'cantidad.max' => 'La cantidad no puede exceder 10,000 unidades',
            'motivo.max' => 'El motivo no puede exceder 500 caracteres',
            'lote_produccion.max' => 'El lote de producción no puede exceder 100 caracteres',
        ];
    }
}

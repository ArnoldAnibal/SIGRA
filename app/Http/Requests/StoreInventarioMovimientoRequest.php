<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventarioMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'id_producto' =>
            'required|exists:producto_menu,id_producto',

        'tipo' =>
            'required|in:Entrada,Salida',

        'cantidad' =>
            'required|integer|min:1',

        'motivo' =>
            'nullable|string|max:255'
    ];
}
}
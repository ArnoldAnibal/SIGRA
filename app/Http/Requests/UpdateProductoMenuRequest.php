<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoMenuRequest extends FormRequest
{
    // Autoriza la solicitud
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validacion
    public function rules(): array
    {
        return [
            'id_categoria' => 'required|exists:categoria_menu,id_categoria',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'disponible' => 'required|boolean'
        ];
    }
}
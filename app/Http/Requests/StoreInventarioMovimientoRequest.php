<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventarioMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validación para crear un movimiento de inventario. Estas reglas definen los requisitos que deben cumplir los datos enviados en la solicitud para crear un nuevo movimiento de inventario, como el tipo de datos, la longitud máxima y si el campo es opcional o requerido. Al utilizar este request, se asegura que los datos enviados para crear un nuevo movimiento de inventario cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
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
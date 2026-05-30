<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar un movimiento de inventario. Este request define las reglas de validación que se aplicarán a los datos enviados en la solicitud para actualizar un movimiento de inventario, asegurando que los datos sean correctos y cumplan con los requisitos establecidos antes de ser procesados por el controlador.
class UpdateInventarioMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validación para actualizar un movimiento de inventario. Estas reglas definen los requisitos que deben cumplir los datos enviados en la solicitud para actualizar un movimiento de inventario, como el tipo de datos, la longitud máxima y si el campo es opcional o requerido. Al utilizar este request, se asegura que los datos enviados para actualizar un movimiento de inventario cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
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
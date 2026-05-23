<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar un cliente existente. Este request define las reglas de validación para los campos "nombre", "nit" y "direccion". El campo "nombre" es opcional, pero si se proporciona, debe ser una cadena de texto y no puede exceder los 100 caracteres. El campo "nit" es opcional, pero si se proporciona, debe ser una cadena de texto y no puede exceder los 25 caracteres. El campo "direccion" también es opcional, pero si se proporciona, debe ser una cadena de texto y no puede exceder los 255 caracteres. Al utilizar este request, se asegura que los datos enviados para actualizar un cliente existente cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:100',
            'nit' => 'nullable|string|max:25',
            'direccion' => 'nullable|string|max:255'
        ];
    }
}
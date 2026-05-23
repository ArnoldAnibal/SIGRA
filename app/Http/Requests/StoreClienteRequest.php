<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al crear un nuevo cliente. Este request define las reglas de validación para los campos "nombre", "nit" y "direccion". El campo "nombre" es obligatorio, debe ser una cadena de texto y no puede exceder los 100 caracteres. El campo "nit" es opcional, pero si se proporciona, debe ser una cadena de texto y no puede exceder los 25 caracteres. El campo "direccion" también es opcional, pero si se proporciona, debe ser una cadena de texto y no puede exceder los 255 caracteres. Al utilizar este request, se asegura que los datos enviados para crear un nuevo cliente cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'nit' => 'nullable|string|max:25',
            'direccion' => 'nullable|string|max:255'
        ];
    }
}
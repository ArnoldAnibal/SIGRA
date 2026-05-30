<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar un proveedor. Este request define las reglas de validación que se aplicarán a los datos enviados en la solicitud para actualizar un proveedor, asegurando que los datos sean correctos y cumplan con los requisitos establecidos antes de ser procesados por el controlador.
class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validación para actualizar un proveedor. Estas reglas definen los requisitos que deben cumplir los datos enviados en la solicitud para actualizar un proveedor, como el tipo de datos, la longitud máxima y si el campo es opcional o requerido.
    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:150',
            'telefono' => 'nullable|string|max:25',
            'direccion' => 'nullable|string|max:255',
        ];
    }
}
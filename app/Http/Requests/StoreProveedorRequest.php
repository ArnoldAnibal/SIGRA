<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al crear un proveedor. Este request define las reglas de validación que se aplicarán a los datos enviados en la solicitud para crear un nuevo proveedor, asegurando que los datos sean correctos y cumplan con los requisitos establecidos antes de ser procesados por el controlador.
class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'nombre' => 'required|string|max:150',
        'telefono' => 'nullable|string|max:25',
        'direccion' => 'nullable|string|max:255'
    ];
}
}
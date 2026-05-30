<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:150',
            'telefono' => 'nullable|string|max:25',
            'direccion' => 'nullable|string|max:255',
        ];
    }
}
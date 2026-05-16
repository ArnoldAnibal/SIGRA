<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Form Request para validar los datos de entrada al crear una nueva categoría de menú. Este Form Request asegura que el campo 'nombre' sea obligatorio, sea una cadena de texto, tenga un máximo de 100 caracteres y sea único en la tabla 'categoria_menu' para evitar duplicados.
class StoreCategoriaMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100|unique:categoria_menu,nombre'
        ];
    }
}

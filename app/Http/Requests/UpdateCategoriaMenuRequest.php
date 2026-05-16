<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Form Request para validar los datos de entrada al actualizar una categoría de menú. Este Form Request asegura que el campo 'nombre' sea obligatorio, sea una cadena de texto y tenga un máximo de 100 caracteres. A diferencia del Form Request para crear una categoría, no se requiere que el nombre sea único, ya que al actualizar una categoría existente, es posible que el nombre no cambie o que se permita duplicados.
class UpdateCategoriaMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100'
        ];
    }
}
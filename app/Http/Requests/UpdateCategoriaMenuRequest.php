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
    // Get the validation rules that apply to the request. Este método devuelve un arreglo de reglas de validación para el campo 'nombre'. Estas reglas aseguran que el nombre sea obligatorio, sea una cadena de texto y tenga un máximo de 100 caracteres. A diferencia del Form Request para crear una categoría, no se requiere que el nombre sea único, ya que al actualizar una categoría existente, es posible que el nombre no cambie o que se permita duplicados.
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100'
        ];
    }
}
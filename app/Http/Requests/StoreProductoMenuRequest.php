<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoMenuRequest extends FormRequest
{
    // Autoriza la solicitud para crear un nuevo producto de menú. En este caso, se permite que cualquier usuario pueda realizar esta solicitud, ya que el método devuelve true. En un escenario real, podrías implementar lógica de autorización más compleja para restringir el acceso a ciertos usuarios o roles.    
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validacion para crear un nuevo producto de menú. Este método devuelve un arreglo de reglas de validación para los campos 'id_categoria', 'nombre', 'descripcion', 'precio_venta' y 'disponible'. Estas reglas aseguran que el ID de categoría sea obligatorio y exista en la tabla 'categoria_menu', el nombre sea obligatorio, sea una cadena de texto, tenga un máximo de 100 caracteres y sea único en la tabla 'producto_menu', la descripción sea opcional y sea una cadena de texto, el precio de venta sea obligatorio, sea un número y tenga un valor mínimo de 0, y el estado de disponibilidad sea obligatorio y sea un valor booleano (true o false).
    public function rules(): array
    {
        return [
            'id_categoria' => 'required|exists:categoria_menu,id_categoria',
            'nombre' => 'required|string|max:100|unique:producto_menu,nombre',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'disponible' => 'required|boolean'
        ];
    }
}
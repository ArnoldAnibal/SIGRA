<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request. Este método devuelve un arreglo de reglas de validación para los campos 'numero_mesa', 'capacidad' y 'estado'. Estas reglas aseguran que el número de mesa sea un entero único, la capacidad sea un entero con un valor mínimo de 1 y el estado sea uno de los valores permitidos (Libre, Ocupada, Reservada o Mantenimiento). Al utilizar este request, se asegura que los datos enviados para crear una nueva mesa cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
    public function rules(): array
    {
        return [
            'numero_mesa' => 'required|integer|unique:mesa,numero_mesa',

            'capacidad' => 'required|integer|min:1',

            'estado' => 'required|in:Libre,Ocupada,Reservada,Mantenimiento'
        ];
    }

    // Método para personalizar los mensajes de error de validación. En este caso, se personaliza el mensaje para la regla 'in' del campo 'estado', indicando que el estado debe ser uno de los valores permitidos (Libre, Ocupada, Reservada o Mantenimiento). Al definir este método, se mejora la claridad de los mensajes de error que se devuelven al cliente cuando los datos no cumplen con las reglas de validación.
    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser: Libre, Ocupada, Reservada o Mantenimiento.',
            'numero_mesa.unique' => 'El número de mesa ya está en uso.',
            'capacidad.min' => 'La capacidad debe ser un número entero positivo.'
        ];
    }
}
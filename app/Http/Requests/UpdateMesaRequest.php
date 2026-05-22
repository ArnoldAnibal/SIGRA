<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request. Este método devuelve un arreglo de reglas de validación para los campos 'numero', 'capacidad' y 'disponible'. Estas reglas aseguran que el número de mesa sea un entero, la capacidad sea un entero y el estado de disponibilidad sea un valor booleano. A diferencia del Form Request para crear una mesa, se utiliza la regla 'sometimes' para indicar que estos campos son opcionales al actualizar una mesa, lo que permite actualizar solo algunos de los campos sin requerir que todos estén presentes.
    public function rules(): array
    {
        return [
            'numero' => 'sometimes|integer',
            'capacidad' => 'sometimes|integer',
            'disponible' => 'sometimes|boolean'
        ];
    }
}
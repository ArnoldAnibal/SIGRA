<?php

namespace App\Http\Requests;

// Form Request para validar los datos de entrada al crear una nueva mesa. Este Form Request asegura que el campo 'numero' sea obligatorio y sea un número entero, el campo 'capacidad' sea obligatorio y sea un número entero, y el campo 'disponible' sea obligatorio y sea un valor booleano (true o false).
use Illuminate\Foundation\Http\FormRequest;

class StoreMesaRequest extends FormRequest
{
    // Determine if the user is authorized to make this request. En este caso, se permite que cualquier usuario pueda realizar esta solicitud, ya que el método devuelve true. En un escenario real, podrías implementar lógica de autorización más compleja para restringir el acceso a ciertos usuarios o roles.
    public function authorize(): bool
    {
        return true;
    }
    // Get the validation rules that apply to the request. Este método devuelve un arreglo de reglas de validación para los campos 'numero', 'capacidad' y 'disponible'. Estas reglas aseguran que el número de mesa sea un entero, la capacidad sea un entero y el estado de disponibilidad sea un valor booleano.
     
    public function rules(): array
    {
        return [
            'numero' => 'required|integer',
            'capacidad' => 'required|integer',
            'disponible' => 'required|boolean'
        ];
    }
}
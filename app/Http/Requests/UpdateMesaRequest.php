<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar una mesa existente. Este request define las reglas de validación para los campos "numero_mesa", "capacidad" y "estado". El campo "numero_mesa" es obligatorio, debe ser un número entero y no puede estar vacío. El campo "capacidad" también es obligatorio, debe ser un número entero y debe tener un valor mínimo de 1. El campo "estado" es obligatorio, pero solo puede tomar uno de los siguientes valores: "Libre", "Ocupada", "Reservada" o "Mantenimiento". Al utilizar este request, se asegura que los datos enviados para actualizar una mesa existente cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class UpdateMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero_mesa' => 'required|integer',

            'capacidad' => 'required|integer|min:1',

            'estado' => 'required|in:Libre,Ocupada,Reservada,Mantenimiento'
        ];
    }

    // Método para personalizar los mensajes de error de validación. En este caso, se personaliza el mensaje para la regla "in" del campo "estado", indicando que el estado debe ser uno de los valores permitidos (Libre, Ocupada, Reservada o Mantenimiento). Al definir este método, se mejora la claridad de los mensajes de error que se devuelven al cliente cuando los datos no cumplen con las reglas de validación.
    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser: Libre, Ocupada, Reservada o Mantenimiento.',
            'numero_mesa.integer' => 'El número de mesa debe ser un número entero.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad debe ser un número entero positivo.'
        ];
    }
}
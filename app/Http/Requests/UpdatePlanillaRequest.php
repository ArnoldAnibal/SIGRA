<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al crear una nueva planilla. Este request define las reglas de validación para los campos "id_empleado", "periodo" y "salario_neto". El campo "id_empleado" es obligatorio y debe existir en la tabla "empleado". El campo "periodo" es obligatorio, debe ser una cadena de texto y no puede exceder los 50 caracteres. El campo "salario_neto" es obligatorio, debe ser un número y debe ser mayor o igual a 0. Al utilizar este request, se asegura que los datos enviados para crear una nueva planilla cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class UpdatePlanillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validación para actualizar una planilla. Estas reglas definen los requisitos que deben cumplir los datos enviados en la solicitud para actualizar una planilla, como el tipo de datos, la longitud máxima y si el campo es opcional o requerido. Al utilizar este request, se asegura que los datos enviados para actualizar una planilla cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
    public function rules(): array
    {
        return [
            'id_empleado' => 'sometimes|exists:empleado,id_empleado',
            'periodo' => 'sometimes|string|max:50',
            'salario_neto' => 'sometimes|numeric|min:0'
        ];
    }
}
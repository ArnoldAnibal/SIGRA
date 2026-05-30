<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar Clock In.
// Ya NO recibe id_empleado desde el frontend.
// El controlador AsistenciaController se encargará de obtener el id_empleado del token de autenticación del usuario que realiza la solicitud, asegurando que solo los empleados autenticados puedan registrar su asistencia. Este request define las reglas de validación para los campos "fecha", "hora_entrada" y "estado". El campo "fecha" es obligatorio y debe ser una fecha válida. El campo "hora_entrada" es obligatorio y debe ser una hora válida. El campo "estado" es obligatorio y debe ser uno de los siguientes valores: "Entrada" o "Salida". Al utilizar este request, se asegura que los datos enviados para registrar la asistencia cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class StoreAsistenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        ];
    }
}
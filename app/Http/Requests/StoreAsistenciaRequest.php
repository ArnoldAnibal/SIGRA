<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar la creación de una nueva asistencia
class StoreAsistenciaRequest extends FormRequest
{
    // Permitir que cualquier usuario pueda realizar esta solicitud (ajustar según necesidades de autenticación)
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validación para los campos necesarios al crear una asistencia
    public function rules(): array
    {
        return [
            'id_empleado' => 'required|exists:empleado,id_empleado'
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Form Request para validar los datos de entrada al actualizar una asistencia. Actualmente no se requieren reglas de validación específicas para la actualización de una asistencia, ya que en este caso solo se permite actualizar la hora de salida y el estado, y estos campos pueden ser opcionales. Sin embargo, si en el futuro se agregan más campos o se requieren validaciones adicionales, este Form Request se puede modificar para incluir las reglas necesarias.
class UpdateAsistenciaRequest extends FormRequest
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
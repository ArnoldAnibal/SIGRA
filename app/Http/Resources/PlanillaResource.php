<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo Planilla en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos de la planilla, incluyendo "id_planilla", "id_empleado", "periodo", "salario_neto", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de las planillas se devuelvan de manera consistente y estructurada en las respuestas de la API.
class PlanillaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_planilla' => $this->id_planilla,
            'id_empleado' => $this->id_empleado,
            'periodo' => $this->periodo,
            'salario_neto' => $this->salario_neto,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
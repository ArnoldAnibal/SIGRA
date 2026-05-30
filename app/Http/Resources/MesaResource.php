<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo Mesa en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos de la mesa, incluyendo "id", "numero", "capacidad", "disponible", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de las mesas se devuelvan de manera consistente y estructurada en las respuestas de la API.
class MesaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'capacidad' => $this->capacidad,
            'disponible' => $this->disponible,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
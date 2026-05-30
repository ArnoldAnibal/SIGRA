<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para representar un proveedor en las respuestas de la API. Este recurso transforma los datos del modelo Proveedor en un formato adecuado para ser devuelto en las respuestas JSON de la API.
class ProveedorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_proveedor' => $this->id_proveedor,
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
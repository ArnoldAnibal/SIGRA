<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo InventarioMovimiento en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos del movimiento de inventario, incluyendo "id", "id_producto", "tipo", "cantidad", "motivo", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de los movimientos de inventario se devuelvan de manera consistente y estructurada en las respuestas de la API.
class InventarioMovimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_producto' => $this->id_producto,
            'tipo' => $this->tipo,
            'cantidad' => $this->cantidad,
            'motivo' => $this->motivo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
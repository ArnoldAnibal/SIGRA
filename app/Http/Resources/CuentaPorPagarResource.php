<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo CuentaPorPagar en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos de la cuenta por pagar, incluyendo "id", "id_proveedor", "monto", "descripcion", "estado", "fecha_vencimiento", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de las cuentas por pagar se devuelvan de manera consistente y estructurada en las respuestas de la API.
class CuentaPorPagarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_proveedor' => $this->id_proveedor,
            'monto' => $this->monto,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
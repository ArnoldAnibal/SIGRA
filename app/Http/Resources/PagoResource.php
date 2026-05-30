<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo Pago en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos del pago, incluyendo "id_pago", "id_factura", "monto", "metodo_pago", "detalle_pago", "fecha_pago", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de los pagos se devuelvan de manera consistente y estructurada en las respuestas de la API.
class PagoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id_pago' => $this->id_pago,

            'id_factura' => $this->id_factura,

            'monto' => $this->monto,

            'metodo_pago' => $this->metodo_pago,

            'detalle_pago' => $this->detalle_pago,

            'fecha_pago' => $this->fecha_pago,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at
        ];
    }
}
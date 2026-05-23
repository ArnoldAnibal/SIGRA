<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo FacturaElectronica en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos de la factura electrónica, incluyendo "id_factura", "uuid_sat", "fecha_emision", "nit_receptor", "monto_total", "estado", "id_pedido", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de las facturas electrónicas se devuelvan de manera consistente y estructurada en las respuestas de la API.
class FacturaElectronicaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_factura' => $this->id_factura,
            'uuid_sat' => $this->uuid_sat,
            'fecha_emision' => $this->fecha_emision,
            'nit_receptor' => $this->nit_receptor,
            'monto_total' => $this->monto_total,
            'estado' => $this->estado,
            'id_pedido' => $this->id_pedido,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
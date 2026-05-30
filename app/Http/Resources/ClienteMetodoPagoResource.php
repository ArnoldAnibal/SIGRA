<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo ClienteMetodoPago en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos del método de pago del cliente, incluyendo "id_cliente_metodo_pago", "id_cliente", "metodo_pago", "detalle_pago", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de los métodos de pago de los clientes se devuelvan de manera consistente y estructurada en las respuestas de la API.
class ClienteMetodoPagoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}

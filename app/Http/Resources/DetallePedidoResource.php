<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo DetallePedido en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos del detalle de pedido, incluyendo "id_detalle", "id_pedido", "id_producto", "cantidad", "subtotal", "notas", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de los detalles de pedido se devuelvan de manera consistente y estructurada en las respuestas de la API.
class DetallePedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_detalle' => $this->id_detalle,
            'id_pedido' => $this->id_pedido,
            'id_producto' => $this->id_producto,
            'cantidad' => $this->cantidad,
            'subtotal' => $this->subtotal,
            'notas' => $this->notas,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
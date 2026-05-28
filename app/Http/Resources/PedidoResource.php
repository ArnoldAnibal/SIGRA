<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo Pedido en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos del pedido, incluyendo "id_pedido", "estado", "tipo", "total", "id_mesa", "id_cliente", "id_empleado", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de los pedidos se devuelvan de manera consistente y estructurada en las respuestas de la API.
class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pedido' => $this->id_pedido,
            'estado' => $this->estado,
            'tipo' => $this->tipo,
            'total' => $this->total,
            'id_mesa' => $this->id_mesa,
            'id_cliente' => $this->id_cliente,
            'id_empleado' => $this->id_empleado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'direccion_entrega' => $this->direccion_entrega,
            'telefono_contacto' => $this->telefono_contacto,
            'es_delivery' => $this->es_delivery,
            'metodo_pago' => $this->metodo_pago,
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Resource para transformar los datos de una factura electrónica en un formato adecuado para la respuesta de la API. Este recurso define cómo se deben presentar los datos de una factura electrónica cuando se devuelven en las respuestas de la API, asegurando que solo se incluyan los campos relevantes y que se estructuren de manera clara y consistente para los consumidores de la API.
class FacturaElectronicaResource extends JsonResource
{
    // Transform the resource into an array. Este método define cómo se deben transformar los datos de una factura electrónica en un arreglo que se incluirá en la respuesta JSON de la API. En este caso, se incluyen campos como el ID de la factura, el UUID del SAT, la fecha de emisión, el NIT del receptor, el monto total, el estado, el método de pago, el estado de pago, el detalle de pago, el ID del cliente deudor, el ID del pedido, y las fechas de creación y actualización. Esta transformación asegura que los datos se presenten de manera clara y estructurada para los consumidores de la API.
    public function toArray(Request $request): array
    {
        return [
            'id_factura' => $this->id_factura,

            'uuid_sat' => $this->uuid_sat,

            'fecha_emision' => $this->fecha_emision,

            'nit_receptor' => $this->nit_receptor,

            'monto_total' => $this->monto_total,

            'estado' => $this->estado,

            'metodo_pago' => $this->metodo_pago,

            'estado_pago' => $this->estado_pago,

            'detalle_pago' => $this->detalle_pago,

            'id_cliente_deudor' => $this->id_cliente_deudor,

            'id_pedido' => $this->id_pedido,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at
        ];
    }
}
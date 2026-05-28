<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
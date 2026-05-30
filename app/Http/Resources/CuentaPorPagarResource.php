<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
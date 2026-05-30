<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
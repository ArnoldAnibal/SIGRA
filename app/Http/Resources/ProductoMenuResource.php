<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoMenuResource extends JsonResource
{
    /**
     * Transformar el recurso en un arreglo.
     */
    public function toArray(Request $request): array
    {
        return [
            'id_producto' => $this->id_producto,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio_venta' => $this->precio_venta,
            'disponible' => $this->disponible,

            // Informacion de la categoria relacionada
            'categoria' => [
                'id_categoria' => $this->categoria->id_categoria,
                'nombre' => $this->categoria->nombre
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
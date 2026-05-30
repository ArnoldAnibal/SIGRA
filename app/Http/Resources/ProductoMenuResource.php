<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para representar un producto del menú en las respuestas de la API. Este recurso transforma los datos del modelo ProductoMenu en un formato adecuado para ser devuelto en las respuestas JSON de la API, incluyendo información de la categoría relacionada y la URL de la imagen del producto si está disponible.
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
            'imagen_url' => $this->imagen
            ? asset('storage/' . $this->imagen)
            : null,

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
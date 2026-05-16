<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso de API para formatear la salida de las categorías de menú. Este recurso define cómo se deben presentar los datos de una categoría de menú cuando se devuelven en las respuestas JSON de la API. Incluye los campos 'id_categoria', 'nombre', 'created_at' y 'updated_at' para proporcionar información completa sobre cada categoría de menú.
class CategoriaMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_categoria' => $this->id_categoria,
            'nombre' => $this->nombre,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}

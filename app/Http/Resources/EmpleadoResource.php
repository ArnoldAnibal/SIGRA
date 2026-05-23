<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Recurso para transformar los datos del modelo Empleado en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos del empleado, incluyendo "id_empleado", "nombre", "rol", "username", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de los empleados se devuelvan de manera consistente y estructurada en las respuestas de la API.
class EmpleadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Devuelve un array con los campos del empleado que se deben incluir en la respuesta de la API. Esto incluye el ID del empleado, su nombre, rol, nombre de usuario y las fechas de creación y actualización. El campo "password" se oculta automáticamente debido a la configuración del modelo Empleado, que lo incluye en la propiedad $hidden.
        return [
            'id_empleado' => $this->id_empleado,
            'nombre' => $this->nombre,
            'rol' => $this->rol,
            'username' => $this->username,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
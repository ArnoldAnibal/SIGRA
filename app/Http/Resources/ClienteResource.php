<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


// Recurso para transformar los datos del modelo Cliente en un formato adecuado para ser devuelto en las respuestas de la API. Este recurso define cómo se deben presentar los campos del cliente, incluyendo "id_cliente", "nombre", "nit", "direccion", "created_at" y "updated_at". Al utilizar este recurso, se asegura que los datos de los clientes se devuelvan de manera consistente y estructurada en las respuestas de la API.
class ClienteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id_cliente' => $this->id_cliente,
            'nombre' => $this->nombre,
            'nit' => $this->nit,
            'direccion' => $this->direccion,

            'tipo_cliente' => $this->tipo_cliente,

            'username' => $this->username,

            'email' => $this->email,

            'telefono' => $this->telefono,

            'limite_credito' => $this->limite_credito,

            'saldo_credito_actual' => $this->saldo_credito_actual,

            'credito_disponible' =>
                $this->limite_credito - $this->saldo_credito_actual,

            'estado' => $this->estado,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
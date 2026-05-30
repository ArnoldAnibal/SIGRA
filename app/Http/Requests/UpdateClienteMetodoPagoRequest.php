<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar un método de pago de cliente. Este request define las reglas de validación que se aplicarán a los datos enviados en la solicitud para actualizar un método de pago de cliente, asegurando que los datos sean correctos y cumplan con los requisitos establecidos antes de ser procesados por el controlador.
class UpdateClienteMetodoPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cliente' =>
                'required|exists:cliente,id_cliente',

            'tipo_metodo' =>
                'required|string|max:50',

            'titular' =>
                'required|string|max:150',

            'ultimos_4' =>
                'nullable|string|max:4',

            'token_pasarela' =>
                'nullable|string|max:255',

            'activo' =>
                'required|boolean'
        ];
    }
}
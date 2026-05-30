<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
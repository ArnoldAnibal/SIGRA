<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero' => 'sometimes|integer',
            'capacidad' => 'sometimes|integer',
            'disponible' => 'sometimes|boolean'
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero' => 'required|integer',
            'capacidad' => 'required|integer',
            'disponible' => 'required|boolean'
        ];
    }
}
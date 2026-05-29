<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para Clock Out.
class UpdateAsistenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        ];
    }
}
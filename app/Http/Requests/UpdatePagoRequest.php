<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'monto' =>
                'sometimes|numeric|min:0.01',

            'metodo_pago' =>
                'sometimes|in:Efectivo,Tarjeta,Transferencia',

            'detalle_pago' =>
                'sometimes|nullable|string|max:150',

            'fecha_pago' =>
                'sometimes|date'
        ];
    }
}
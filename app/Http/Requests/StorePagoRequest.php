<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'id_factura' =>
                'required|exists:factura_electronica,id_factura',

            'monto' =>
                'required|numeric|min:0.01',

            'metodo_pago' =>
                'required|in:Efectivo,Tarjeta,Transferencia',

            'detalle_pago' =>
                'nullable|string|max:150',

            'fecha_pago' =>
                'required|date'
        ];
    }
}
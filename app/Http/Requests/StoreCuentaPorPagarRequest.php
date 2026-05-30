<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuentaPorPagarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'id_proveedor' => 'required|exists:proveedor,id_proveedor',
        'monto' => 'required|numeric|min:0.01',
        'descripcion' => 'nullable|string|max:255',
        'estado' => 'required|in:Pendiente,Pagado',
        'fecha_vencimiento' => 'nullable|date'
    ];
}
}
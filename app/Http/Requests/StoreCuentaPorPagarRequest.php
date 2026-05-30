<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al crear una cuenta por pagar. Este request define las reglas de validación que se aplicarán a los datos enviados en la solicitud para crear una nueva cuenta por pagar, asegurando que los datos sean correctos y cumplan con los requisitos establecidos antes de ser procesados por el controlador.
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
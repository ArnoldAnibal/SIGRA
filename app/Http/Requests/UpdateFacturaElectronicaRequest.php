<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar una factura electrónica existente. Este request define las reglas de validación para los campos "uuid_sat", "fecha_emision", "nit_receptor", "monto_total", "estado" e "id_pedido". El campo "uuid_sat" es opcional, pero si se proporciona, debe ser una cadena de texto y no puede exceder los 100 caracteres. El campo "fecha_emision" es opcional, pero si se proporciona, debe ser una fecha válida. El campo "nit_receptor" es opcional, pero si se proporciona, debe ser una cadena de texto y no puede exceder los 25 caracteres. El campo "monto_total" es opcional, pero si se proporciona, debe ser un número válido y no puede ser negativo. El campo "estado" es opcional, pero si se proporciona, debe ser uno de los valores permitidos: 'Emitida' o 'Anulada'. El campo "id_pedido" es opcional, pero si se proporciona, debe existir en la tabla "pedido". Al utilizar este request, se asegura que los datos enviados para actualizar una factura electrónica existente cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class UpdateFacturaElectronicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid_sat' => 'sometimes|string|max:100',
            'fecha_emision' => 'sometimes|date',
            'nit_receptor' => 'sometimes|string|max:25',
            'monto_total' => 'sometimes|numeric|min:0',
            'estado' => 'sometimes|in:Emitida,Anulada',
            'id_pedido' => 'sometimes|exists:pedido,id_pedido'
        ];
    }

    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser: Emitida o Anulada.',
            'uuid_sat.unique' => 'El UUID del SAT ya está en uso.',
            'monto_total.min' => 'El monto total debe ser un número positivo.'
        ];
    }
}
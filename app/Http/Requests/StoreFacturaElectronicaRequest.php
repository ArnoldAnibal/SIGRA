<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al crear una nueva factura electrónica. Este request define las reglas de validación para los campos "uuid_sat", "fecha_emision", "nit_receptor", "monto_total", "estado" e "id_pedido". El campo "uuid_sat" es obligatorio, debe ser una cadena de texto, no puede exceder los 100 caracteres y debe ser único en la tabla "factura_electronica". El campo "fecha_emision" es obligatorio y debe ser una fecha válida. El campo "nit_receptor" es obligatorio, debe ser una cadena de texto y no puede exceder los 25 caracteres. El campo "monto_total" es obligatorio, debe ser un número y debe ser mayor o igual a 0. El campo "estado" es obligatorio y debe ser uno de los siguientes valores: "Emitida" o "Anulada". El campo "id_pedido" es obligatorio y debe existir en la tabla "pedido". Al utilizar este request, se asegura que los datos enviados para crear una nueva factura electrónica cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class StoreFacturaElectronicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid_sat' => 'required|string|max:100|unique:factura_electronica,uuid_sat',
            'fecha_emision' => 'required|date',
            'nit_receptor' => 'required|string|max:25',
            'monto_total' => 'required|numeric|min:0',
            'estado' => 'required|in:Emitida,Anulada',
            'id_pedido' => 'required|exists:pedido,id_pedido'
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
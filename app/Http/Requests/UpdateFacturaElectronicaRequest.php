<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturaElectronicaRequest extends FormRequest
{
    // Permitir que cualquier usuario pueda realizar esta solicitud (ajustar según necesidades de autenticación)
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request. Este método devuelve un arreglo de reglas de validación para los campos necesarios al actualizar una factura electrónica. Estas reglas aseguran que el UUID del SAT sea único (excluyendo la factura actual), que la fecha de emisión sea una fecha válida, que el NIT del receptor sea una cadena de texto con un máximo de 25 caracteres, que el monto total sea un número positivo, que el estado sea Emitida o Anulada, que el método de pago sea uno de los valores permitidos (Efectivo, Tarjeta, Transferencia o Credito), que el estado de pago sea Pendiente o Pagada, y que el detalle de pago sea obligatorio si el método de pago es Tarjeta, Transferencia o Credito. Además, se permite que algunos campos sean opcionales (usando 'sometimes'), lo que significa que solo se validarán si están presentes en la solicitud, lo que facilita la actualización parcial de una factura electrónica sin requerir que todos los campos estén presentes.
    public function rules(): array
{
    return [
        'uuid_sat' => 'sometimes|string|max:100',

        'fecha_emision' => 'sometimes|date',

        'fecha_vencimiento' => 'nullable|date',

        'nit_receptor' => 'sometimes|string|max:25',

        'monto_total' => 'sometimes|numeric|min:0',

        'estado' => 'sometimes|in:Emitida,Anulada',

        'metodo_pago' =>
            'sometimes|nullable|in:Efectivo,Tarjeta,Transferencia,Credito',

        'detalle_pago' =>
            'sometimes|nullable|string|max:150',

        'estado_pago' =>
            'sometimes|in:Pendiente,Pagada',

        'id_cliente_deudor' =>
            'sometimes|nullable|exists:cliente,id_cliente',

        'id_pedido' =>
            'sometimes|exists:pedido,id_pedido'
    ];
}

    // Definir mensajes de error personalizados para las reglas de validación. Este método devuelve un arreglo de mensajes de error personalizados para cada regla de validación definida en el método rules(). Estos mensajes proporcionan información clara y específica sobre por qué una solicitud no cumple con las reglas de validación, lo que facilita a los usuarios entender y corregir los errores en sus solicitudes.
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (
                $this->metodo_pago === 'Efectivo' &&
                !is_null($this->detalle_pago)
            ) {
                $validator->errors()->add(
                    'detalle_pago',
                    'El detalle de pago debe ser null cuando el método es Efectivo.'
                );
            }
        });
    }

    // Definir mensajes de error personalizados para las reglas de validación. Este método devuelve un arreglo de mensajes de error personalizados para cada regla de validación definida en el método rules(). Estos mensajes proporcionan información clara y específica sobre por qué una solicitud no cumple con las reglas de validación, lo que facilita a los usuarios entender y corregir los errores en sus solicitudes.
    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser: Emitida o Anulada.',

            'uuid_sat.unique' => 'El UUID del SAT ya está en uso.',

            'monto_total.min' => 'El monto total debe ser un número positivo.',

            'metodo_pago.in' =>
                'El método de pago debe ser: Efectivo, Tarjeta, Transferencia o Credito.',

            'estado_pago.in' =>
                'El estado de pago debe ser: Pendiente o Pagada.',

            'detalle_pago.required_if' =>
                'El detalle de pago es obligatorio para Tarjeta, Transferencia y Credito.'
        ];
    }
}
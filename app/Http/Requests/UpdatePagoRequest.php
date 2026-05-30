<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar un pago. Este request define las reglas de validación que se aplicarán a los datos enviados en la solicitud para actualizar un pago, asegurando que los datos sean correctos y cumplan con los requisitos establecidos antes de ser procesados por el controlador.
class UpdatePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validación para actualizar un pago. Estas reglas definen los requisitos que deben cumplir los datos enviados en la solicitud para actualizar un pago, como el tipo de datos, la longitud máxima y si el campo es opcional o requerido. Al utilizar este request, se asegura que los datos enviados para actualizar un pago cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
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
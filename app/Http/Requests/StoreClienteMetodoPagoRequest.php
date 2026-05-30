<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al crear un método de pago de cliente. Este request define las reglas de validación que se aplicarán a los datos enviados en la solicitud para crear un nuevo método de pago de cliente, asegurando que los datos sean correctos y cumplan con los requisitos establecidos antes de ser procesados por el controlador.
class StoreClienteMetodoPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validación para crear un método de pago de cliente. Estas reglas definen los requisitos que deben cumplir los datos enviados en la solicitud para crear un nuevo método de pago de cliente, como el tipo de datos, la longitud máxima y si el campo es opcional o requerido. Al utilizar este request, se asegura que los datos enviados para crear un nuevo método de pago de cliente cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
    public function rules(): array
    {
        return [
            'id_cliente' =>
                'required|exists:cliente,id_cliente',

            'tipo_metodo' =>
                'required|string|max:50',

            'titular' =>
                'required|string|max:150',

            'ultimos_4' =>
                'nullable|string|max:4',

            'token_pasarela' =>
                'nullable|string|max:255',

            'activo' =>
                'required|boolean'
        ];
    }
}
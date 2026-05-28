<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al crear un nuevo pedido. Este request define las reglas de validación para los campos "estado", "tipo", "total", "id_mesa", "id_cliente" e "id_empleado". El campo "estado" es obligatorio y debe ser uno de los siguientes valores: "Pendiente", "Preparando", "Preparado", "Entregado" o "Cancelado". El campo "tipo" es obligatorio y debe ser uno de los siguientes valores: "Para Llevar", "Para Aca" o "Online". El campo "total" es obligatorio, debe ser un número y no puede ser negativo. El campo "id_mesa" es opcional, pero si se proporciona, debe existir en la tabla "mesa". El campo "id_cliente" es opcional, pero si se proporciona, debe existir en la tabla "cliente". El campo "id_empleado" es obligatorio y debe existir en la tabla "empleado". Al utilizar este request, se asegura que los datos enviados para crear un nuevo pedido cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Reglas de validación para los campos del pedido. Estas reglas aseguran que los datos enviados para crear un nuevo pedido sean válidos y cumplan con los requisitos establecidos antes de ser procesados en el controlador.
    public function rules(): array
{
    return [
        'estado' => 'required|in:Pendiente,Preparando,Preparado,Entregado,Cancelado',

        'tipo' => 'required|in:Para Llevar,Para Aca,Online',

        'total' => 'required|numeric|min:0',

        'id_mesa' => 'nullable|exists:mesa,id_mesa',

        'id_cliente' => 'nullable|exists:cliente,id_cliente',

        'id_empleado' => 'required|exists:empleado,id_empleado',

        'direccion_entrega' => 'required_if:tipo,Online|nullable|string|max:255',

        'telefono_contacto' => 'nullable|string|max:25',

        'metodo_pago' =>
            'required|in:Efectivo,Tarjeta,Transferencia,Credito'
    ];
}

    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser: Pendiente, Preparando, Preparado, Entregado o Cancelado.',
            'tipo.in' => 'El tipo debe ser: Para Llevar, Para Aca o Online.',
            'total.min' => 'El total no puede ser negativo.',
            'id_mesa.exists' => 'La mesa seleccionada no existe.',
            'id_cliente.exists' => 'El cliente seleccionado no existe.',
            'id_empleado.exists' => 'El empleado seleccionado no existe.'
        ];
    }
}
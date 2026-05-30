<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar un pedido existente. Este request define las reglas de validación para los campos "estado", "tipo", "total", "id_mesa", "id_cliente" e "id_empleado". El campo "estado" es opcional, pero si se proporciona, debe ser uno de los siguientes valores: "Pendiente", "Preparando", "Preparado", "Entregado" o "Cancelado". El campo "tipo" es opcional, pero si se proporciona, debe ser uno de los siguientes valores: "Para Llevar", "Para Aca" o "Online". El campo "total" es opcional, pero si se proporciona, debe ser un número y no puede ser negativo. El campo "id_mesa" es opcional, pero si se proporciona, debe existir en la tabla "mesa". El campo "id_cliente" es opcional, pero si se proporciona, debe existir en la tabla "cliente". El campo "id_empleado" es opcional, pero si se proporciona, debe existir en la tabla "empleado". Al utilizar este request, se asegura que los datos enviados para actualizar un pedido existente cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class UpdatePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => 'sometimes|in:Pendiente,Preparando,Preparado,Entregado,Cancelado',
            'tipo' => 'sometimes|in:Para Llevar,Para Aca,Online',
            'total' => 'sometimes|numeric|min:0',
            'id_mesa' => 'nullable|exists:mesa,id_mesa',
            'id_cliente' => 'nullable|exists:cliente,id_cliente',
            'id_empleado' => 'sometimes|exists:empleado,id_empleado',
            'metodo_pago' => 'sometimes|in:Efectivo,Tarjeta,Transferencia,Credito'
        ];
    }

    // Mensajes de error personalizados para las reglas de validación. Este método devuelve un arreglo de mensajes de error personalizados que se mostrarán cuando una regla de validación falle. Por ejemplo, si el campo "estado" no es uno de los valores permitidos, se mostrará el mensaje "El estado debe ser: Pendiente, Preparando, Preparado, Entregado o Cancelado.". Al definir estos mensajes personalizados, se proporciona una retroalimentación más clara y específica a los usuarios sobre los errores de validación en sus solicitudes.
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
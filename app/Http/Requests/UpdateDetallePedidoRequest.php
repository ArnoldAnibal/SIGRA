<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar un detalle de pedido existente. Este request define las reglas de validación para los campos "id_pedido", "id_producto", "cantidad", "subtotal" y "notas". El campo "id_pedido" es opcional, pero si se proporciona, debe existir en la tabla "pedido". El campo "id_producto" es opcional, pero si se proporciona, debe existir en la tabla "producto_menu". El campo "cantidad" es opcional, pero si se proporciona, debe ser un entero y debe tener un valor mínimo de 1. El campo "subtotal" es opcional, pero si se proporciona, debe ser un número y debe tener un valor mínimo de 0. El campo "notas" es opcional, pero si se proporciona, debe ser una cadena de texto. Al utilizar este request, se asegura que los datos enviados para actualizar un detalle de pedido existente cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class UpdateDetallePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pedido' => 'sometimes|exists:pedido,id_pedido',
            'id_producto' => 'sometimes|exists:producto_menu,id_producto',
            'cantidad' => 'sometimes|integer|min:1',
            'subtotal' => 'sometimes|numeric|min:0',
            'notas' => 'nullable|string'
        ];
    }
}
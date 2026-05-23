<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "detalle_pedido" con los campos "id_detalle", "id_pedido", "id_producto", "cantidad", "subtotal" y "notas". El campo "id_detalle" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable.
class DetallePedido extends Model
{
    use SoftDeletes;

    protected $table = 'detalle_pedido';

    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_pedido',
        'id_producto',
        'cantidad',
        'subtotal',
        'notas'
    ];
}
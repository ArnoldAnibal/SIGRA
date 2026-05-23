<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "pedido" con los campos "id_pedido", "estado", "tipo", "total", "id_mesa", "id_cliente" y "id_empleado". El campo "id_pedido" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable.
class Pedido extends Model
{
    use SoftDeletes;

    protected $table = 'pedido';

    protected $primaryKey = 'id_pedido';

    // $fillable define los campos que se pueden asignar masivamente cuando se crea o actualiza un registro. Esto ayuda a proteger contra asignaciones masivas no deseadas.
    protected $fillable = [
        'estado',
        'tipo',
        'total',
        'id_mesa',
        'id_cliente',
        'id_empleado'
    ];
}
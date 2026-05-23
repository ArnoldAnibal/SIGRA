<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "cliente" con los campos "id_cliente", "nombre", "nit" y "direccion". El campo "id_cliente" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros.

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'cliente';

    protected $primaryKey = 'id_cliente';

    // $fillable define los campos que se pueden asignar masivamente cuando se crea o actualiza un registro. Esto ayuda a proteger contra asignaciones masivas no deseadas.
    protected $fillable = [
        'nombre',
        'nit',
        'direccion'
    ];
}
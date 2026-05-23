<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "categoria_menu" con los campos "id_categoria" y "nombre". El campo "id_categoria" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable.
class CategoriaMenu extends Model
{
    use SoftDeletes;

    protected $table = 'categoria_menu';

    protected $primaryKey = 'id_categoria';

    // $fillable define los campos que se pueden asignar masivamente cuando se crea o actualiza un registro. Esto ayuda a proteger contra asignaciones masivas no deseadas.
    protected $fillable = [
        'nombre'
    ];
}
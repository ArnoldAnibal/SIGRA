<?php

namespace App\Models;

// Modelo Eloquent para la tabla 'mesa'. Este modelo representa las mesas en la base de datos y define las propiedades y configuraciones necesarias para interactuar con la tabla. Incluye el nombre de la tabla, la clave primaria, los campos que se pueden asignar masivamente y el uso de soft deletes para permitir la eliminación lógica de registros sin eliminarlos físicamente de la base de datos.

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    use SoftDeletes;

    protected $table = 'mesa';

    protected $primaryKey = 'id_mesa';

    protected $fillable = [
        'numero_mesa',
        'capacidad',
        'estado'
    ];
}
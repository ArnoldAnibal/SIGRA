<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "empleado" con los campos "id_empleado", "nombre", "rol", "username" y "password". El campo "id_empleado" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable y se ocultan los campos sensibles como "password" y "remember_token" utilizando $hidden.
class Empleado extends Model
{
    use SoftDeletes;

    protected $table = 'empleado';

    protected $primaryKey = 'id_empleado';

    protected $fillable = [
        'nombre',
        'rol',
        'username',
        'password'
    ];

    // $hidden define los campos que se deben ocultar cuando se convierte el modelo a un array o JSON. Esto es útil para proteger información sensible, como contraseñas, al devolver datos del empleado en las respuestas de la API.
    protected $hidden = [
        'password',
        'remember_token'
    ];
}
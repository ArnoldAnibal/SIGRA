<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "planilla" con los campos "id_planilla", "id_empleado", "periodo" y "salario_neto". El campo "id_planilla" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable.
class Planilla extends Model
{
    use SoftDeletes;

    protected $table = 'planilla';

    protected $primaryKey = 'id_planilla';

    protected $fillable = [
        'id_empleado',
        'periodo',
        'salario_neto'
    ];
}
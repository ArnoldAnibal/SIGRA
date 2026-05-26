<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


// Modelo para la tabla de asistencia, que registra las entradas y salidas de los empleados
class Asistencia extends Model
{
    use SoftDeletes;

    // Especificamos el nombre de la tabla
    protected $table = 'asistencia';

    protected $primaryKey = 'id_asistencia';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_empleado',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'estado'
    ];

    // Relación con empleado (una asistencia pertenece a un empleado)
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }
}
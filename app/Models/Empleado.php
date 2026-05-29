<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

// Modelo para la tabla "empleado".
// Este modelo utiliza autenticación con Sanctum para manejo de tokens API.

class Empleado extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'empleado';

    protected $primaryKey = 'id_empleado';

    protected $fillable = [
        'nombre',
        'rol',
        'username',
        'password'
    ];

    // Campos ocultos al convertir a JSON
    protected $hidden = [
        'password',
        'remember_token'
    ];
}
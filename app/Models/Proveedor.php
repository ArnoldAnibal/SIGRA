<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo para la tabla "proveedor" con los campos "id_proveedor", "nombre", "telefono" y "direccion". El campo "id_proveedor" es la clave primaria y se autoincrementa. Además, se definen los campos que se pueden asignar masivamente a través de $fillable, así como la relación con el modelo CuentaPorPagar.
class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedor';

    protected $primaryKey = 'id_proveedor';

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion'
    ];

    public function cuentasPorPagar()
    {
        return $this->hasMany(
            CuentaPorPagar::class,
            'id_proveedor',
            'id_proveedor'
        );
    }
}
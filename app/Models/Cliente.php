<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

// Modelo para la tabla "cliente" con los campos "id_cliente", "nombre", "nit", "direccion", "tipo_cliente", "username", "password", "email", "telefono", "limite_credito", "saldo_credito_actual" y "estado". El campo "id_cliente" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable, así como los campos ocultos y los casts para los campos numéricos.
class Cliente extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'cliente';

    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'tipo_cliente',
        'username',
        'password',
        'email',
        'telefono',
        'limite_credito',
        'saldo_credito_actual',
        'estado'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'saldo_credito_actual' => 'decimal:2'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function pedidos()
    {
        return $this->hasMany(
            Pedido::class,
            'id_cliente'
        );
    }

    public function facturasCredito()
    {
        return $this->hasMany(
            FacturaElectronica::class,
            'id_cliente_deudor'
        );
    }

    public function metodosPago()
    {
        return $this->hasMany(
            ClienteMetodoPago::class,
            'id_cliente'
        );
    }
}
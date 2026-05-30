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

    // Relación uno a muchos con el modelo Pedido. Un cliente puede tener muchos pedidos, pero cada pedido pertenece a un solo cliente. La relación se establece utilizando la clave foránea "id_cliente" en la tabla "pedido" que hace referencia a la clave primaria "id_cliente" en la tabla "cliente".

    public function pedidos()
    {
        return $this->hasMany(
            Pedido::class,
            'id_cliente'
        );
    }

    // Relación uno a muchos con el modelo FacturaElectronica. Un cliente puede ser deudor en muchas facturas electrónicas, pero cada factura electrónica tiene un solo cliente deudor. La relación se establece utilizando la clave foránea "id_cliente_deudor" en la tabla "factura_electronica" que hace referencia a la clave primaria "id_cliente" en la tabla "cliente".
    public function facturasCredito()
    {
        return $this->hasMany(
            FacturaElectronica::class,
            'id_cliente_deudor'
        );
    }

    // Relación uno a muchos con el modelo ClienteMetodoPago. Un cliente puede tener muchos métodos de pago, pero cada método de pago pertenece a un solo cliente. La relación se establece utilizando la clave foránea "id_cliente" en la tabla "cliente_metodo_pago" que hace referencia a la clave primaria "id_cliente" en la tabla "cliente".
    public function metodosPago()
    {
        return $this->hasMany(
            ClienteMetodoPago::class,
            'id_cliente'
        );
    }
}
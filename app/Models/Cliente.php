<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

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
        'password'
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'saldo_credito_actual' => 'decimal:2'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_cliente');
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
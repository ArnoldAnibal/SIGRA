<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $table = 'pedido';

    protected $primaryKey = 'id_pedido';

    protected $fillable = [
        'estado',
        'tipo',
        'total',
        'id_mesa',
        'id_cliente',
        'id_empleado',
        'direccion_entrega',
        'telefono_contacto',
        'es_delivery',
        'metodo_pago'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function factura()
    {
        return $this->hasOne(FacturaElectronica::class, 'id_pedido');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }
}
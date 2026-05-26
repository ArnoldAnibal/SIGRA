<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla de factura electrónica, que representa las facturas emitidas por el sistema. Este modelo define los campos que se pueden asignar masivamente, las relaciones con otros modelos (como Pedido y Cliente), y utiliza SoftDeletes para permitir la eliminación lógica de las facturas sin perder los datos en la base de datos.
class FacturaElectronica extends Model
{
    use SoftDeletes;

    protected $table = 'factura_electronica';

    protected $primaryKey = 'id_factura';

    protected $fillable = [
        'uuid_sat',
        'fecha_emision',
        'nit_receptor',
        'monto_total',
        'estado',
        'metodo_pago',
        'estado_pago',
        'detalle_pago',
        'id_cliente_deudor',
        'id_pedido'
    ];

    // Relación con pedido (una factura pertenece a un pedido)
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    // Relación con cliente deudor (una factura puede pertenecer a un cliente deudor)
    public function clienteDeudor()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_deudor');
    }
}
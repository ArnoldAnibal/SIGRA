<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturaElectronica extends Model
{
    use SoftDeletes;

    protected $table = 'factura_electronica';

    protected $primaryKey = 'id_factura';

    protected $fillable = [
        'uuid_sat',
        'fecha_emision',
        'fecha_vencimiento',
        'nit_receptor',
        'monto_total',
        'estado',
        'metodo_pago',
        'estado_pago',
        'detalle_pago',
        'id_cliente_deudor',
        'id_pedido',
        'fecha_pago'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function clienteDeudor()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_deudor');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_factura');
    }
}
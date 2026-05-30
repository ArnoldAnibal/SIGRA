<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "factura_electronica" con los campos "id_factura", "uuid_sat", "fecha_emision", "fecha_vencimiento", "nit_receptor", "monto_total", "estado", "metodo_pago", "estado_pago", "detalle_pago", "id_cliente_deudor", "id_pedido" y "fecha_pago". El campo "id_factura" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable, así como las relaciones con los modelos Pedido, Cliente y Pago.
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

    // Relación muchos a uno con el modelo Pedido. Una factura electrónica pertenece a un pedido, pero un pedido puede tener muchas facturas electrónicas. La relación se establece utilizando la clave foránea "id_pedido" en la tabla "factura_electronica" que hace referencia a la clave primaria "id_pedido" en la tabla "pedido".
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    // Relación muchos a uno con el modelo Cliente. Una factura electrónica puede tener un cliente deudor asociado, pero un cliente puede ser deudor en muchas facturas electrónicas. La relación se establece utilizando la clave foránea "id_cliente_deudor" en la tabla "factura_electronica" que hace referencia a la clave primaria "id_cliente" en la tabla "cliente".
    public function clienteDeudor()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_deudor');
    }

    // Relación uno a muchos con el modelo Pago. Una factura electrónica puede tener muchos pagos asociados, pero cada pago pertenece a una sola factura electrónica. La relación se establece utilizando la clave foránea "id_factura" en la tabla "pago" que hace referencia a la clave primaria "id_factura" en la tabla "factura_electronica".
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_factura');
    }
}
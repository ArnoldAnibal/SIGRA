<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "pedido" con los campos "id_pedido", "estado", "tipo", "total", "id_mesa", "id_cliente", "id_empleado", "direccion_entrega", "telefono_contacto", "es_delivery" y "metodo_pago". El campo "id_pedido" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable, así como las relaciones con los modelos Cliente, FacturaElectronica y DetallePedido.
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

    // Relación muchos a uno con el modelo Cliente. Un pedido pertenece a un cliente, pero un cliente puede tener muchos pedidos. La relación se establece utilizando la clave foránea "id_cliente" en la tabla "pedido" que hace referencia a la clave primaria "id_cliente" en la tabla "cliente".
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    // Relación uno a uno con el modelo FacturaElectronica. Un pedido tiene una factura electrónica asociada, y cada factura electrónica pertenece a un solo pedido. La relación se establece utilizando la clave foránea "id_pedido" en la tabla "factura_electronica" que hace referencia a la clave primaria "id_pedido" en la tabla "pedido".
    public function factura()
    {
        return $this->hasOne(FacturaElectronica::class, 'id_pedido');
    }

    // Relación uno a muchos con el modelo DetallePedido. Un pedido puede tener muchos detalles de pedido, pero cada detalle de pedido pertenece a un solo pedido. La relación se establece utilizando la clave foránea "id_pedido" en la tabla "detalle_pedido" que hace referencia a la clave primaria "id_pedido" en la tabla "pedido".
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }
}
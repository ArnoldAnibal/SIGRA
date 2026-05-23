<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "factura_electronica" con los campos "id_factura", "uuid_sat", "fecha_emision", "nit_receptor", "monto_total", "estado" e "id_pedido". El campo "id_factura" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable.
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
        'id_pedido'
    ];
}
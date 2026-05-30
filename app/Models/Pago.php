<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo para la tabla "pago" con los campos "id_pago", "id_factura", "monto", "metodo_pago", "detalle_pago" y "fecha_pago". El campo "id_pago" es la clave primaria y se autoincrementa. Además, se definen los campos que se pueden asignar masivamente a través de $fillable, así como la relación con el modelo FacturaElectronica.
class Pago extends Model
{
    protected $table = 'pago';

    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_factura',
        'monto',
        'metodo_pago',
        'detalle_pago',
        'fecha_pago'
    ];

    public function factura()
    {
        return $this->belongsTo(
            FacturaElectronica::class,
            'id_factura'
        );
    }
}
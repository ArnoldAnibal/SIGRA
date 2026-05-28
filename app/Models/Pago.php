<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
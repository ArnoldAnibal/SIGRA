<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo para representar las cuentas por pagar a proveedores
class CuentaPorPagar extends Model
{
    use HasFactory;

    protected $table = 'cuenta_por_pagar';

    protected $fillable = [
        'id_proveedor',
        'monto',
        'descripcion',
        'estado',
        'fecha_vencimiento'
    ];

    public function proveedor()
    {
        return $this->belongsTo(
            Proveedor::class,
            'id_proveedor',
            'id_proveedor'
        );
    }
}
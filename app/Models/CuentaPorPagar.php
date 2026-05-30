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

    // Relación muchos a uno con el modelo Proveedor. Una cuenta por pagar pertenece a un proveedor, pero un proveedor puede tener muchas cuentas por pagar. La relación se establece utilizando la clave foránea "id_proveedor" en la tabla "cuenta_por_pagar" que hace referencia a la clave primaria "id_proveedor" en la tabla "proveedor".
    public function proveedor()
    {
        return $this->belongsTo(
            Proveedor::class,
            'id_proveedor',
            'id_proveedor'
        );
    }
}
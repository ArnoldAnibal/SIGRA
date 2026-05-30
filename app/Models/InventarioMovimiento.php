<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo para la tabla "inventario_movimiento" con los campos "id_movimiento", "id_producto", "tipo", "cantidad" y "motivo". El campo "id_movimiento" es la clave primaria y se autoincrementa. Además, se definen los campos que se pueden asignar masivamente a través de $fillable, así como la relación con el modelo ProductoMenu.
class InventarioMovimiento extends Model
{
    use HasFactory;

    protected $table = 'inventario_movimiento';

    protected $fillable = [
        'id_producto',
        'tipo',
        'cantidad',
        'motivo'
    ];

    public function producto()
    {
        return $this->belongsTo(
            ProductoMenu::class,
            'id_producto',
            'id_producto'
        );
    }
}
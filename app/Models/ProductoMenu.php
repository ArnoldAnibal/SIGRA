<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo para la tabla "producto_menu" con los campos "id_producto", "id_categoria", "nombre", "descripcion", "precio_venta", "disponible", "stock", "stock_minimo" e "imagen". El campo "id_producto" es la clave primaria y se autoincrementa. El modelo utiliza soft deletes para permitir la eliminación lógica de los registros. Además, se definen los campos que se pueden asignar masivamente a través de $fillable, así como las relaciones con el modelo CategoriaMenu y el modelo InventarioMovimiento.
class ProductoMenu extends Model
{
    use SoftDeletes;

    protected $table = 'producto_menu';

    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'id_categoria',
        'nombre',
        'descripcion',
        'precio_venta',
        'disponible',
        'stock',
        'stock_minimo',
        'imagen'
    ];

    public function categoria()
    {
        return $this->belongsTo(
            CategoriaMenu::class,
            'id_categoria',
            'id_categoria'
        );
    }

// Relación uno a muchos con el modelo InventarioMovimiento. Un producto puede tener muchos movimientos de inventario, pero cada movimiento de inventario pertenece a un solo producto. La relación se establece utilizando la clave foránea "id_producto" en la tabla "inventario_movimiento" que hace referencia a la clave primaria "id_producto" en la tabla "producto_menu".
    public function movimientosInventario()
{
    return $this->hasMany(
        InventarioMovimiento::class,
        'id_producto',
        'id_producto'
    );
}

}
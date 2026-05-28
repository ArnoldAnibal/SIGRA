<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
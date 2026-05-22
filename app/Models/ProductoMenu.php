<?php

namespace App\Models;

// Modelo Eloquent para la tabla 'producto_menu'. Este modelo representa los productos de menú en la base de datos y define las propiedades y configuraciones necesarias para interactuar con la tabla. Incluye el nombre de la tabla, la clave primaria, los campos que se pueden asignar masivamente y la relación con la categoría de menú a través del método 'categoria()'. Además, utiliza soft deletes para permitir la eliminación lógica de registros sin eliminarlos físicamente de la base de datos.

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
        'disponible'
    ];

    // Un producto pertenece a una categoria de menú. Este método define una relación de tipo "pertenece a" entre el modelo ProductoMenu y el modelo CategoriaMenu. La relación se establece utilizando la clave foránea 'id_categoria' en la tabla 'producto_menu' que hace referencia a la clave primaria 'id_categoria' en la tabla 'categoria_menu'. Esto permite acceder a la categoría de menú asociada a un producto de menú utilizando la propiedad 'categoria' en una instancia del modelo ProductoMenu.
    public function categoria()
    {
        return $this->belongsTo(
            CategoriaMenu::class,
            'id_categoria',
            'id_categoria'
        );
    }
}
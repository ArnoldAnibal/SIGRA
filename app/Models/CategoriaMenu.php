<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo Eloquent para la tabla 'categoria_menu'. Este modelo representa las categorías de menú en la base de datos y define las propiedades y configuraciones necesarias para interactuar con la tabla. Incluye el nombre de la tabla, la clave primaria, el tipo de clave y los campos que se pueden asignar masivamente.
class CategoriaMenu extends Model
{
    protected $table = 'categoria_menu';

    protected $primaryKey = 'id_categoria';

    public $incrementing = true;

    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente al crear o actualizar una categoría de menú. En este caso, solo el campo 'nombre' es asignable masivamente.
    protected $fillable = [
        'nombre'
    ];
}
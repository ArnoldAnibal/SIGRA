<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    use SoftDeletes;

    protected $table = 'mesa';

    protected $primaryKey = 'id_mesa';

    protected $fillable = [
        'numero_mesa',
        'capacidad',
        'estado'
    ];
}
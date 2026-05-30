<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo para la tabla "cliente_metodo_pago" con los campos "id_metodo_pago", "id_cliente", "tipo_metodo", "titular", "ultimos_4", "token_pasarela" y "activo". El campo "id_metodo_pago" es la clave primaria y se autoincrementa. Además, se definen los campos que se pueden asignar masivamente a través de $fillable, así como la relación con el modelo Cliente.
class ClienteMetodoPago extends Model
{
    protected $table = 'cliente_metodo_pago';

    protected $primaryKey = 'id_metodo_pago';

    protected $fillable = [
        'id_cliente',
        'tipo_metodo',
        'titular',
        'ultimos_4',
        'token_pasarela',
        'activo'
    ];

    // Relación muchos a uno con el modelo Cliente. Un método de pago pertenece a un cliente, pero un cliente puede tener muchos métodos de pago. La relación se establece utilizando la clave foránea "id_cliente" en la tabla "cliente_metodo_pago" que hace referencia a la clave primaria "id_cliente" en la tabla "cliente".
    public function cliente()
    {
        return $this->belongsTo(
            Cliente::class,
            'id_cliente'
        );
    }
}
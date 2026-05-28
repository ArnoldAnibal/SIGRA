<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function cliente()
    {
        return $this->belongsTo(
            Cliente::class,
            'id_cliente'
        );
    }
}
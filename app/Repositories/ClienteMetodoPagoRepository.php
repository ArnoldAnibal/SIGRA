<?php

namespace App\Repositories;

use App\Models\ClienteMetodoPago;

class ClienteMetodoPagoRepository
{
    public function getAll()
    {
        return ClienteMetodoPago::all();
    }

    public function findById(int $id)
    {
        return ClienteMetodoPago::find($id);
    }

    public function create(array $data)
    {
        return ClienteMetodoPago::create($data);
    }

    public function update(
        ClienteMetodoPago $metodo,
        array $data
    ) {
        $metodo->update($data);

        return $metodo;
    }

    public function delete(ClienteMetodoPago $metodo)
    {
        return $metodo->delete();
    }
}
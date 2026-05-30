<?php

namespace App\Repositories;

use App\Models\ClienteMetodoPago;

// Repositorio para manejar las operaciones relacionadas con los métodos de pago de los clientes. Este repositorio proporciona métodos para obtener todos los métodos de pago, encontrar un método de pago por su ID, crear un nuevo método de pago, actualizar un método de pago existente y eliminar un método de pago.

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
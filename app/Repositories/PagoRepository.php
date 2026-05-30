<?php

namespace App\Repositories;

use App\Models\Pago;

// Repositorio para manejar las operaciones relacionadas con los pagos realizados por los clientes. Este repositorio proporciona métodos para obtener todos los pagos, encontrar un pago por su ID, crear un nuevo pago, actualizar un pago existente y eliminar un pago.
class PagoRepository
{
    public function getAll()
    {
        return Pago::all();
    }

    public function findById(int $id)
    {
        return Pago::find($id);
    }

    public function create(array $data)
    {
        return Pago::create($data);
    }

    public function update(Pago $pago, array $data)
    {
        $pago->update($data);

        return $pago;
    }

    public function delete(Pago $pago)
    {
        return $pago->delete();
    }
}
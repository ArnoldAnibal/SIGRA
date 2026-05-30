<?php

namespace App\Repositories;

use App\Models\CuentaPorPagar;

// Repositorio para manejar las operaciones relacionadas con las cuentas por pagar a proveedores. Este repositorio proporciona métodos para obtener todas las cuentas por pagar, encontrar una cuenta por su ID, crear una nueva cuenta, actualizar una cuenta existente y eliminar una cuenta.
class CuentaPorPagarRepository
{
    public function getAll()
    {
        return CuentaPorPagar::all();
    }

    public function findById(int $id)
    {
        return CuentaPorPagar::find($id);
    }

    public function create(array $data)
    {
        return CuentaPorPagar::create($data);
    }

    public function update(
        CuentaPorPagar $cuenta,
        array $data
    ) {
        $cuenta->update($data);

        return $cuenta;
    }

    public function delete(CuentaPorPagar $cuenta)
    {
        return $cuenta->delete();
    }
}
<?php

namespace App\Repositories;

use App\Models\InventarioMovimiento;

// Repositorio para manejar las operaciones relacionadas con los movimientos de inventario. Este repositorio proporciona métodos para obtener todos los movimientos, encontrar un movimiento por su ID, crear un nuevo movimiento, actualizar un movimiento existente y eliminar un movimiento.
class InventarioMovimientoRepository
{
    public function getAll()
    {
        return InventarioMovimiento::all();
    }

    public function findById(int $id)
    {
        return InventarioMovimiento::find($id);
    }

    public function create(array $data)
    {
        return InventarioMovimiento::create($data);
    }

    public function update(
        InventarioMovimiento $movimiento,
        array $data
    ) {
        $movimiento->update($data);

        return $movimiento;
    }

    public function delete(
        InventarioMovimiento $movimiento
    ) {
        return $movimiento->delete();
    }
}
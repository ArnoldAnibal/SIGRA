<?php

namespace App\Repositories;

use App\Models\InventarioMovimiento;

// Repositorio para manejar las operaciones relacionadas con los movimientos de inventario. Este repositorio proporciona métodos para obtener todos los movimientos, encontrar un movimiento por su ID, crear un nuevo movimiento, actualizar un movimiento existente y eliminar un movimiento.
class InventarioMovimientoRepository
{
    // Método para obtener todos los movimientos de inventario. Utiliza el método all del modelo InventarioMovimiento para obtener todos los movimientos de la base de datos y luego los devuelve.
    public function getAll()
    {
        return InventarioMovimiento::all();
    }

    //  Método para encontrar un movimiento de inventario por su ID. Recibe el ID del movimiento y utiliza el método find del modelo InventarioMovimiento para obtener el movimiento correspondiente de la base de datos y luego lo devuelve.
    public function findById(int $id)
    {
        return InventarioMovimiento::find($id);
    }

    // Método para crear un nuevo movimiento de inventario. Recibe un array de datos y utiliza el método create del modelo InventarioMovimiento para crear el movimiento en la base de datos y devuelve el movimiento creado.
    public function create(array $data)
    {
        return InventarioMovimiento::create($data);
    }

    // Método para actualizar un movimiento de inventario existente. Recibe el movimiento a actualizar y un array de datos con los nuevos valores, utiliza el método update del modelo InventarioMovimiento para actualizar el movimiento en la base de datos y devuelve el movimiento actualizado.
    public function update(
        InventarioMovimiento $movimiento,
        array $data
    ) {
        $movimiento->update($data);

        return $movimiento;
    }

    // Método para eliminar un movimiento de inventario. Recibe el movimiento a eliminar, utiliza el método delete del modelo InventarioMovimiento para eliminar el movimiento de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(
        InventarioMovimiento $movimiento
    ) {
        return $movimiento->delete();
    }
}
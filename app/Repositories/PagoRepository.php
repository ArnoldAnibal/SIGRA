<?php

namespace App\Repositories;

use App\Models\Pago;

// Repositorio para manejar las operaciones relacionadas con los pagos realizados por los clientes. Este repositorio proporciona métodos para obtener todos los pagos, encontrar un pago por su ID, crear un nuevo pago, actualizar un pago existente y eliminar un pago.
class PagoRepository
{
    // Método para obtener todos los pagos. Utiliza el método all del modelo Pago para obtener todos los pagos de la base de datos y luego los devuelve.
    public function getAll()
    {
        return Pago::all();
    }

    // Método para encontrar un pago por su ID. Recibe el ID del pago y utiliza el método find del modelo Pago para obtener el pago correspondiente de la base de datos y luego lo devuelve.
    public function findById(int $id)
    {
        return Pago::find($id);
    }

    // Método para crear un nuevo pago. Recibe un array de datos y utiliza el método create del modelo Pago para crear el pago en la base de datos y devuelve el pago creado.
    public function create(array $data)
    {
        return Pago::create($data);
    }

    // Método para actualizar un pago existente. Recibe el pago a actualizar y un array de datos con los nuevos valores, utiliza el método update del modelo Pago para actualizar el pago en la base de datos y devuelve el pago actualizado.
    public function update(Pago $pago, array $data)
    {
        $pago->update($data);

        return $pago;
    }

    // Método para eliminar un pago. Recibe el pago a eliminar, utiliza el método delete del modelo Pago para eliminar el pago de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(Pago $pago)
    {
        return $pago->delete();
    }
}
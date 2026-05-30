<?php

namespace App\Repositories;

use App\Models\ClienteMetodoPago;

// Repositorio para manejar las operaciones relacionadas con los métodos de pago de los clientes. Este repositorio proporciona métodos para obtener todos los métodos de pago, encontrar un método de pago por su ID, crear un nuevo método de pago, actualizar un método de pago existente y eliminar un método de pago.

class ClienteMetodoPagoRepository
{
    // Método para obtener todos los métodos de pago de los clientes. Utiliza el método all del modelo ClienteMetodoPago para obtener todas las entradas de la base de datos y luego las devuelve.
    public function getAll()
    {
        return ClienteMetodoPago::all();
    }

    // Método para encontrar un método de pago por su ID. Recibe el ID del método de pago y utiliza el método find del modelo ClienteMetodoPago para obtener la entrada correspondiente de la base de datos y luego la devuelve.
    public function findById(int $id)
    {
        return ClienteMetodoPago::find($id);
    }

    // Método para crear un nuevo método de pago. Recibe un array de datos y utiliza el método create del modelo ClienteMetodoPago para crear la entrada en la base de datos y devuelve el método de pago creado.
    public function create(array $data)
    {
        return ClienteMetodoPago::create($data);
    }

    // Método para actualizar un método de pago existente. Recibe el método de pago a actualizar y un array de datos con los nuevos valores, utiliza el método update del modelo ClienteMetodoPago para actualizar la entrada en la base de datos y devuelve el método de pago actualizado.
    public function update(
        ClienteMetodoPago $metodo,
        array $data
    ) {
        $metodo->update($data);

        return $metodo;
    }

    // Método para eliminar un método de pago. Recibe el método de pago a eliminar, utiliza el método delete del modelo ClienteMetodoPago para eliminar la entrada de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(ClienteMetodoPago $metodo)
    {
        return $metodo->delete();
    }
}
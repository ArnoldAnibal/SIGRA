<?php

namespace App\Repositories;

use App\Models\Cliente;

// Repositorio para manejar las operaciones relacionadas con el modelo Cliente. Este repositorio proporciona métodos para obtener todos los clientes, encontrar un cliente por su ID, crear un nuevo cliente, actualizar un cliente existente y eliminar un cliente. El uso de un repositorio ayuda a mantener el código organizado y facilita la reutilización de la lógica de acceso a datos en diferentes partes de la aplicación.

class ClienteRepository
{
    // Método para obtener todos los clientes de la base de datos.
    public function getAll()
    {
        return Cliente::all();
    }

    // Método para encontrar un cliente por su ID. Devuelve el cliente si se encuentra, o null si no se encuentra.
    public function findById(int $id)
    {
        return Cliente::find($id);
    }

    // Método para crear un nuevo cliente en la base de datos. Recibe un array de datos y devuelve el cliente creado.
    public function create(array $data)
    {
        return Cliente::create($data);
    }

    // Método para actualizar un cliente existente. Recibe el cliente a actualizar y un array de datos con los nuevos valores. Devuelve el cliente actualizado.
    public function update(Cliente $cliente, array $data)
    {
        $cliente->update($data);

        return $cliente;
    }

    // Método para eliminar un cliente. Recibe el cliente a eliminar y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(Cliente $cliente)
    {
        return $cliente->delete();
    }
}
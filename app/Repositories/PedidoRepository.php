<?php

namespace App\Repositories;

use App\Models\Pedido;

// Repositorio para el modelo Pedido. Este repositorio proporciona métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) en la tabla "pedido" de la base de datos. Los métodos incluyen getAll() para obtener todos los pedidos, findById() para encontrar un pedido por su ID, create() para crear un nuevo pedido, update() para actualizar un pedido existente y delete() para eliminar un pedido. Al utilizar este repositorio, se abstrae la lógica de acceso a datos del controlador, lo que facilita el mantenimiento y la reutilización del código.
class PedidoRepository
{
    // Método para obtener todos los pedidos de la base de datos.
    public function getAll()
    {
        return Pedido::all();
    }

     // Método para encontrar un pedido por su ID. Devuelve el pedido si se encuentra, o null si no se encuentra.
    public function findById(int $id)
    {
        return Pedido::find($id);
    }

    // Método para crear un nuevo pedido en la base de datos. Recibe un array de datos y devuelve el pedido creado.
    public function create(array $data)
    {
        return Pedido::create($data);
    }

    // Método para actualizar un pedido existente. Recibe el pedido a actualizar y un array de datos con los nuevos valores. Devuelve el pedido actualizado.
    public function update(Pedido $pedido, array $data)
    {
        $pedido->update($data);

        return $pedido;
    }

    // Método para eliminar un pedido. Recibe el pedido a eliminar y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(Pedido $pedido)
    {
        return $pedido->delete();
    }
}
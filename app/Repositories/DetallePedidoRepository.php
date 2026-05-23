<?php

namespace App\Repositories;

use App\Models\DetallePedido;

// Repositorio para manejar las operaciones relacionadas con el modelo DetallePedido. Este repositorio proporciona métodos para obtener todos los detalles de pedido, encontrar un detalle de pedido por su ID, crear un nuevo detalle de pedido, actualizar un detalle de pedido existente y eliminar un detalle de pedido. El uso de un repositorio ayuda a mantener el código organizado y facilita la reutilización de la lógica de acceso a datos en diferentes partes de la aplicación.
class DetallePedidoRepository
{
    // Método para obtener todos los detalles de pedido de la base de datos.
    public function getAll()
    {
        return DetallePedido::all();
    }

    // Método para encontrar un detalle de pedido por su ID. Devuelve el detalle de pedido si se encuentra, o null si no se encuentra.
    public function findById(int $id)
    {
        return DetallePedido::find($id);
    }

    // Método para crear un nuevo detalle de pedido en la base de datos. Recibe un array de datos y devuelve el detalle de pedido creado.
    public function create(array $data)
    {
        return DetallePedido::create($data);
    }

        // Método para actualizar un detalle de pedido existente. Recibe el detalle de pedido a actualizar y un array de datos con los nuevos valores. Devuelve el detalle de pedido actualizado.
    public function update(DetallePedido $detalle, array $data)
    {
        $detalle->update($data);

        return $detalle;
    }

    // Método para eliminar un detalle de pedido. Recibe el detalle de pedido a eliminar y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(DetallePedido $detalle)
    {
        return $detalle->delete();
    }
}
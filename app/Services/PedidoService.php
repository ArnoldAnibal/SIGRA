<?php

namespace App\Services;

use App\Repositories\PedidoRepository;

// Servicio para manejar la lógica de negocio relacionada con los pedidos. Este servicio utiliza el PedidoRepository para interactuar con la base de datos y proporciona métodos para obtener todos los pedidos, obtener un pedido por su ID, crear un nuevo pedido, actualizar un pedido existente y eliminar un pedido. Al utilizar este servicio, se abstrae la lógica de negocio del controlador, lo que facilita el mantenimiento y la reutilización del código.
class PedidoService
{
    protected $repository;

    // Constructor que recibe una instancia de PedidoRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con los pedidos.
    public function __construct(PedidoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para obtener todos los pedidos. Utiliza el método getAll del repositorio para obtener los datos de los pedidos y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener un pedido por su ID. Utiliza el método findById del repositorio para obtener el pedido correspondiente al ID proporcionado y luego lo devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo pedido. Recibe un array de datos, utiliza el método create del repositorio para crear el pedido en la base de datos y devuelve el pedido creado.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Método para actualizar un pedido existente. Recibe el pedido a actualizar y un array de datos con los nuevos valores, utiliza el método update del repositorio para actualizar el pedido en la base de datos y devuelve el pedido actualizado.
    public function update($pedido, array $data)
    {
        return $this->repository->update($pedido, $data);
    }

    // Método para eliminar un pedido. Recibe el pedido a eliminar, utiliza el método delete del repositorio para eliminar el pedido de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($pedido)
    {
        return $this->repository->delete($pedido);
    }
}
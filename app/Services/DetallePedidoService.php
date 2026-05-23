<?php

namespace App\Services;

use App\Repositories\DetallePedidoRepository;

// Servicio para manejar la lógica de negocio relacionada con el modelo DetallePedido. Este servicio utiliza el repositorio DetallePedidoRepository para realizar las operaciones de acceso a datos. El servicio proporciona métodos para obtener todos los detalles de pedido, obtener un detalle de pedido por su ID, crear un nuevo detalle de pedido, actualizar un detalle de pedido existente y eliminar un detalle de pedido. Al utilizar un servicio, se separa la lógica de negocio de la lógica de acceso a datos, lo que mejora la organización del código y facilita su mantenimiento.
class DetallePedidoService
{
    protected $repository;

    // Constructor que recibe una instancia de DetallePedidoRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar las operaciones relacionadas con los detalles de pedido.
    public function __construct(DetallePedidoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para obtener todos los detalles de pedido. Utiliza el método getAll del repositorio para obtener los datos de los detalles de pedido y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener un detalle de pedido por su ID. Utiliza el método findById del repositorio para obtener el detalle de pedido correspondiente al ID proporcionado y luego lo devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo detalle de pedido. Recibe un array de datos, utiliza el método create del repositorio para crear el detalle de pedido en la base de datos y devuelve el detalle de pedido creado.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Método para actualizar un detalle de pedido existente. Recibe el detalle de pedido a actualizar y un array de datos con los nuevos valores, utiliza el método update del repositorio para actualizar el detalle de pedido en la base de datos y devuelve el detalle de pedido actualizado.
    public function update($detalle, array $data)
    {
        return $this->repository->update($detalle, $data);
    }

    // Método para eliminar un detalle de pedido. Recibe el detalle de pedido a eliminar, utiliza el método delete del repositorio para eliminar el detalle de pedido de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($detalle)
    {
        return $this->repository->delete($detalle);
    }
}
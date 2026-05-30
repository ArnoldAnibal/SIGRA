<?php

namespace App\Services;

use App\Repositories\ClienteMetodoPagoRepository;

// Servicio para gestionar los métodos de pago de los clientes, utilizando el repositorio ClienteMetodoPagoRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todos los métodos de pago, encontrar un método de pago por su ID, crear un nuevo método de pago, actualizar un método de pago existente y eliminar un método de pago.
class ClienteMetodoPagoService
{
    protected $repository;

    // Constructor que recibe una instancia de ClienteMetodoPagoRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con los métodos de pago de los clientes.  
    public function __construct(
        ClienteMetodoPagoRepository $repository
    ) {
        $this->repository = $repository;
    }

    // Método para obtener todos los métodos de pago de los clientes. Utiliza el método getAll del repositorio para obtener los datos de los métodos de pago y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener un método de pago de cliente por su ID. Utiliza el método findById del repositorio para obtener el método de pago correspondiente al ID proporcionado y luego lo devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo método de pago de cliente. Recibe un array de datos y utiliza el método create del repositorio para crear el método de pago en la base de datos y devuelve el método de pago creado.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    //  Método para actualizar un método de pago de cliente existente. Recibe el método de pago a actualizar y un array de datos con los nuevos valores, utiliza el método update del repositorio para actualizar el método de pago en la base de datos y devuelve el método de pago actualizado.
    public function update($metodo, array $data)
    {
        return $this->repository->update($metodo, $data);
    }

    // Método para eliminar un método de pago de cliente. Recibe el método de pago a eliminar, utiliza el método delete del repositorio para eliminar el método de pago de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($metodo)
    {
        return $this->repository->delete($metodo);
    }
}
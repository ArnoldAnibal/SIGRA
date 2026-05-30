<?php

namespace App\Services;

use App\Repositories\ClienteMetodoPagoRepository;

// Servicio para gestionar los métodos de pago de los clientes, utilizando el repositorio ClienteMetodoPagoRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todos los métodos de pago, encontrar un método de pago por su ID, crear un nuevo método de pago, actualizar un método de pago existente y eliminar un método de pago.
class ClienteMetodoPagoService
{
    protected $repository;

    public function __construct(
        ClienteMetodoPagoRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($metodo, array $data)
    {
        return $this->repository->update($metodo, $data);
    }

    public function delete($metodo)
    {
        return $this->repository->delete($metodo);
    }
}
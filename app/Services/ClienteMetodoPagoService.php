<?php

namespace App\Services;

use App\Repositories\ClienteMetodoPagoRepository;

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
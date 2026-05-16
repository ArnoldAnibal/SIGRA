<?php

namespace App\Services;

use App\Repositories\MesaRepository;
use App\Http\Resources\MesaResource;

class MesaService
{
    protected $repository;

    public function __construct(MesaRepository $repository)
    {
        $this->repository = $repository;
    }

    // Obtener todas las mesas
    public function getAll()
    {
        return MesaResource::collection(
            $this->repository->getAll()
        );
    }

    // Obtener mesa por ID
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Crear mesa
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Actualizar mesa
    public function update($mesa, array $data)
    {
        return $this->repository->update($mesa, $data);
    }

    // Eliminar mesa
    public function delete($mesa)
    {
        return $this->repository->delete($mesa);
    }
}
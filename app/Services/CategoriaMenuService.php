<?php

namespace App\Services;

use App\Repositories\CategoriaMenuRepository;

class CategoriaMenuService
{
    protected $repository;

    public function __construct(CategoriaMenuRepository $repository)
    {
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

    public function update($categoria, array $data)
    {
        return $this->repository->update($categoria, $data);
    }

    public function delete($categoria)
    {
        return $this->repository->delete($categoria);
    }
}
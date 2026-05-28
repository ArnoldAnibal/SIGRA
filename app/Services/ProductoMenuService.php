<?php

namespace App\Services;

use App\Repositories\ProductoMenuRepository;
use App\Http\Resources\ProductoMenuResource;

class ProductoMenuService
{
    protected $repository;

    public function __construct(ProductoMenuRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return ProductoMenuResource::collection(
            $this->repository->getAll()
        );
    }

    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {
        // 🔥 ensure imagen always exists key-wise (optional safety)
        $data['imagen'] = $data['imagen'] ?? null;

        return $this->repository->create($data);
    }

    public function update($producto, array $data)
    {
        return $this->repository->update($producto, $data);
    }

    public function delete($producto)
    {
        return $this->repository->delete($producto);
    }
}
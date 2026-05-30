<?php

namespace App\Services;

use App\Repositories\ProveedorRepository;

// Servicio para gestionar los proveedores, utilizando el repositorio ProveedorRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todos los proveedores, encontrar un proveedor por su ID, crear un nuevo proveedor, actualizar un proveedor existente y eliminar un proveedor.
class ProveedorService
{
    protected $repository;

    public function __construct(
        ProveedorRepository $repository
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

    public function update(
        $proveedor,
        array $data
    ) {
        return $this->repository->update(
            $proveedor,
            $data
        );
    }

    public function delete($proveedor)
    {
        return $this->repository->delete(
            $proveedor
        );
    }
}
<?php

namespace App\Services;

use App\Repositories\CuentaPorPagarRepository;

// Servicio para gestionar las cuentas por pagar a proveedores, utilizando el repositorio CuentaPorPagarRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todas las cuentas por pagar, encontrar una cuenta por su ID, crear una nueva cuenta, actualizar una cuenta existente y eliminar una cuenta.
class CuentaPorPagarService
{
    protected $repository;

    public function __construct(
        CuentaPorPagarRepository $repository
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
        $cuenta,
        array $data
    ) {
        return $this->repository->update(
            $cuenta,
            $data
        );
    }

    public function delete($cuenta)
    {
        return $this->repository->delete(
            $cuenta
        );
    }
}
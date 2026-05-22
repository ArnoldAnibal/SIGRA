<?php

namespace App\Services;

// Servicio para manejar la lógica de negocio relacionada con la entidad ProductoMenu. Este servicio utiliza el repositorio ProductoMenuRepository para interactuar con la base de datos y realizar operaciones CRUD. El servicio se encarga de procesar los datos y aplicar cualquier lógica de negocio necesaria antes de devolver los resultados a los controladores o recursos que lo consumen.

use App\Repositories\ProductoMenuRepository;
use App\Http\Resources\ProductoMenuResource;

class ProductoMenuService
{
    protected $repository;

    public function __construct(ProductoMenuRepository $repository)
    {
        $this->repository = $repository;
    }

    // Obtener todos los productos
    public function getAll()
    {
        return ProductoMenuResource::collection(
            $this->repository->getAll()
        );
    }

    // Obtener producto por ID
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Crear producto
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Actualizar producto
    public function update($producto, array $data)
    {
        return $this->repository->update($producto, $data);
    }

    // Eliminar producto
    public function delete($producto)
    {
        return $this->repository->delete($producto);
    }
}
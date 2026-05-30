<?php

namespace App\Services;

use App\Repositories\ProductoMenuRepository;
use App\Http\Resources\ProductoMenuResource;

class ProductoMenuService
{
    protected $repository;

    // Constructor que recibe una instancia de ProductoMenuRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con los productos del menú.
    public function __construct(ProductoMenuRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para obtener todos los productos del menú. Utiliza el método getAll del repositorio para obtener los datos de los productos y luego los devuelve como una colección de recursos ProductoMenuResource, lo que permite devolver una respuesta JSON estructurada y consistente para los productos del menú.
    public function getAll()
    {
        return ProductoMenuResource::collection(
            $this->repository->getAll()
        );
    }

    // Método para obtener un producto del menú por su ID. Utiliza el método findById del repositorio para obtener el producto correspondiente al ID proporcionado y luego lo devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo producto del menú. Recibe un array de datos, utiliza el método create del repositorio para crear el producto en la base de datos y devuelve el producto creado.
    public function create(array $data)
    {
        // ensure imagen always exists key-wise (optional safety)
        $data['imagen'] = $data['imagen'] ?? null;

        return $this->repository->create($data);
    }

    // Método para actualizar un producto del menú existente. Recibe el producto a actualizar y un array de datos con los nuevos valores. Devuelve el producto actualizado.
    public function update($producto, array $data)
    {
        return $this->repository->update($producto, $data);
    }

    // Método para eliminar un producto del menú. Recibe el producto a eliminar y lo pasa al método delete del repositorio.
    public function delete($producto)
    {
        return $this->repository->delete($producto);
    }
}
<?php

namespace App\Services;

use App\Repositories\ProveedorRepository;

// Servicio para gestionar los proveedores, utilizando el repositorio ProveedorRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todos los proveedores, encontrar un proveedor por su ID, crear un nuevo proveedor, actualizar un proveedor existente y eliminar un proveedor.
class ProveedorService
{
    protected $repository;

    // Constructor que recibe una instancia de ProveedorRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con los proveedores.
    public function __construct(
        ProveedorRepository $repository
    ) {
        $this->repository = $repository;
    }

    // Método para obtener todos los proveedores. Utiliza el método getAll del repositorio para obtener los datos de los proveedores y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener un proveedor por su ID. Utiliza el método findById del repositorio para obtener el proveedor correspondiente al ID proporcionado y luego lo devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo proveedor. Recibe un array de datos, utiliza el método create del repositorio para crear el proveedor en la base de datos y devuelve el proveedor creado.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Método para actualizar un proveedor existente. Recibe el proveedor a actualizar y un array de datos con los nuevos valores. Devuelve el proveedor actualizado.
    public function update(
        $proveedor,
        array $data
    ) {
        return $this->repository->update(
            $proveedor,
            $data
        );
    }

    // Método para eliminar un proveedor. Recibe el proveedor a eliminar y lo pasa al método delete del repositorio.
    public function delete($proveedor)
    {
        return $this->repository->delete(
            $proveedor
        );
    }
}
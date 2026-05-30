<?php

namespace App\Repositories;

use App\Models\Proveedor;

// Repositorio para manejar las operaciones relacionadas con los proveedores. Este repositorio proporciona métodos para obtener todos los proveedores, encontrar un proveedor por su ID, crear un nuevo proveedor, actualizar un proveedor existente y eliminar un proveedor.
class ProveedorRepository
{
    public function getAll()
    {
        return Proveedor::all();
    }

    public function findById(int $id)
    {
        return Proveedor::find($id);
    }

    public function create(array $data)
    {
        return Proveedor::create($data);
    }

    public function update(Proveedor $proveedor, array $data)
    {
        $proveedor->update($data);

        return $proveedor;
    }

    public function delete(Proveedor $proveedor)
    {
        return $proveedor->delete();
    }
}
<?php

namespace App\Repositories;

use App\Models\Proveedor;

// Repositorio para manejar las operaciones relacionadas con los proveedores. Este repositorio proporciona métodos para obtener todos los proveedores, encontrar un proveedor por su ID, crear un nuevo proveedor, actualizar un proveedor existente y eliminar un proveedor.
class ProveedorRepository
{
    // Método para obtener todos los proveedores. Utiliza el método all del modelo Proveedor para obtener todos los proveedores de la base de datos y luego los devuelve.
    public function getAll()
    {
        return Proveedor::all();
    }

    // Método para encontrar un proveedor por su ID. Recibe el ID del proveedor y utiliza el método find del modelo Proveedor para obtener el proveedor correspondiente de la base de datos y luego lo devuelve.
    public function findById(int $id)
    {
        return Proveedor::find($id);
    }

    // Método para crear un nuevo proveedor. Recibe un array de datos y utiliza el método create del modelo Proveedor para crear el proveedor en la base de datos y devuelve el proveedor creado.
    public function create(array $data)
    {
        return Proveedor::create($data);
    }

    // Método para actualizar un proveedor existente. Recibe el proveedor a actualizar y un array de datos con los nuevos valores, utiliza el método update del modelo Proveedor para actualizar el proveedor en la base de datos y devuelve el proveedor actualizado.
    public function update(Proveedor $proveedor, array $data)
    {
        $proveedor->update($data);

        return $proveedor;
    }

    // Método para eliminar un proveedor. Recibe el proveedor a eliminar, utiliza el método delete del modelo Proveedor para eliminar el proveedor de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(Proveedor $proveedor)
    {
        return $proveedor->delete();
    }
}
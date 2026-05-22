<?php

namespace App\Repositories;

// Repositorio para manejar la lógica de acceso a datos de la entidad ProductoMenu, interactua directamente con la base de datos para realizar operaciones CRUD. Este repositorio es utilizado por el servicio ProductoMenuService para abstraer la lógica de negocio de la lógica de acceso a datos.

use App\Models\ProductoMenu;

class ProductoMenuRepository
{
    // Obtener todos los productos
    public function getAll()
    {
        return ProductoMenu::all();
    }

    // Obtener producto por ID
    public function findById(int $id)
    {
        return ProductoMenu::find($id);
    }

    // Crear producto
    public function create(array $data)
    {
        return ProductoMenu::create($data);
    }

    // Actualizar producto
    public function update(ProductoMenu $producto, array $data)
    {
        $producto->update($data);

        return $producto;
    }

    // Eliminar producto
    public function delete(ProductoMenu $producto)
    {
        return $producto->delete();
    }
}
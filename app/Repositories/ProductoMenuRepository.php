<?php

namespace App\Repositories;

use App\Models\ProductoMenu;

class ProductoMenuRepository
{
    // Obtener todos los productos junto con su categoria
    public function getAll()
    {
        return ProductoMenu::with('categoria')->get();
    }

    // Buscar un producto por ID
    public function findById(int $id)
    {
        return ProductoMenu::with('categoria')->find($id);
    }

    // Crear un nuevo producto
    public function create(array $data)
    {
        return ProductoMenu::create($data);
    }

    // Actualizar un producto existente
    public function update(ProductoMenu $producto, array $data)
    {
        $producto->update($data);

        return $producto;
    }

    // Eliminar un producto
    public function delete(ProductoMenu $producto)
    {
        return $producto->delete();
    }
}
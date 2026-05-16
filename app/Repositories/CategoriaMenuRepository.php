<?php

namespace App\Repositories;

use App\Models\CategoriaMenu;

// Repositorio para manejar la lógica de acceso a datos de la entidad CategoriaMenu, interactua directamente con la base de datos para realizar operaciones CRUD. Este repositorio es utilizado por el servicio CategoriaMenuService para abstraer la lógica de negocio de la lógica de acceso a datos.

class CategoriaMenuRepository
{
    // Devuelve todas las categorías de menú disponibles en la base de datos.
    public function getAll()
    {
        return CategoriaMenu::all();
    }

    // Busca una categoría de menú por su ID. Devuelve la categoría encontrada o null si no existe.
    public function findById(int $id)
    {
        return CategoriaMenu::find($id);
    }

    // Crea una nueva categoría de menú en la base de datos utilizando los datos proporcionados. Devuelve la categoría creada. Reibe el arreglo de datos validados desde el controlador.
    public function create(array $data)
    {
        return CategoriaMenu::create($data);
    }

    // Actualiza una categoría de menú existente con los nuevos datos proporcionados. Devuelve la categoría actualizada. Reibe la instancia de la categoría a actualizar y el arreglo de datos validados desde el controlador.
    public function update(CategoriaMenu $categoria, array $data)
    {
        $categoria->update($data);

        return $categoria;
    }

    // Elimina una categoría de menú existente de la base de datos. Devuelve un booleano indicando si la eliminación fue exitosa. Reibe la instancia de la categoría a eliminar desde el controlador.
    public function delete(CategoriaMenu $categoria)
    {
        return $categoria->delete();
    }
}
<?php

namespace App\Repositories;

use App\Models\CategoriaMenu;

class CategoriaMenuRepository
{
    public function getAll()
    {
        return CategoriaMenu::all();
    }

    public function findById(int $id)
    {
        return CategoriaMenu::find($id);
    }

    public function create(array $data)
    {
        return CategoriaMenu::create($data);
    }

    public function update(CategoriaMenu $categoria, array $data)
    {
        $categoria->update($data);

        return $categoria;
    }

    public function delete(CategoriaMenu $categoria)
    {
        return $categoria->delete();
    }
}
<?php

namespace App\Repositories;

use App\Models\Mesa;

class MesaRepository
{
    // Obtener todas las mesas
    public function getAll()
    {
        return Mesa::all();
    }

    // Obtener mesa por ID
    public function findById(int $id)
    {
        return Mesa::find($id);
    }

    // Crear mesa
    public function create(array $data)
    {
        return Mesa::create($data);
    }

    // Actualizar mesa
    public function update(Mesa $mesa, array $data)
    {
        $mesa->update($data);

        return $mesa;
    }

    // Eliminar mesa
    public function delete(Mesa $mesa)
    {
        return $mesa->delete();
    }
}
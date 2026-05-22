<?php

namespace App\Repositories;

// Repositorio para manejar la lógica de acceso a datos de la entidad Mesa, interactua directamente con la base de datos para realizar operaciones CRUD. Este repositorio es utilizado por el servicio MesaService para abstraer la lógica de negocio de la lógica de acceso a datos.

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
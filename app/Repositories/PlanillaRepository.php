<?php

namespace App\Repositories;

use App\Models\Planilla;

// Repositorio para manejar las operaciones relacionadas con el modelo Planilla. Este repositorio proporciona métodos para obtener todas las planillas, encontrar una planilla por su ID, crear una nueva planilla, actualizar una planilla existente y eliminar una planilla. El uso de un repositorio ayuda a mantener el código organizado y facilita la reutilización de la lógica de acceso a datos en diferentes partes de la aplicación.
class PlanillaRepository
{
    // Método para obtener todas las planillas de la base de datos.
    public function getAll()
    {
        return Planilla::all();
    }

    // Método para encontrar una planilla por su ID. Devuelve la planilla si se encuentra, o null si no se encuentra.
    public function findById(int $id)
    {
        return Planilla::find($id);
    }

    // Método para crear una nueva planilla en la base de datos. Recibe un array de datos y devuelve la planilla creada.
    public function create(array $data)
    {
        return Planilla::create($data);
    }

    // Método para actualizar una planilla existente. Recibe la planilla a actualizar y un array de datos con los nuevos valores. Devuelve la planilla actualizada.
    public function update(Planilla $planilla, array $data)
    {
        $planilla->update($data);

        return $planilla;
    }

    // Método para eliminar una planilla. Recibe la planilla a eliminar y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(Planilla $planilla)
    {
        return $planilla->delete();
    }
}
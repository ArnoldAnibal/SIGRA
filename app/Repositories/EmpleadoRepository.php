<?php

namespace App\Repositories;

use App\Models\Empleado;

// Repositorio para manejar las operaciones relacionadas con el modelo Empleado. Este repositorio proporciona métodos para obtener todos los empleados, encontrar un empleado por su ID, crear un nuevo empleado, actualizar un empleado existente y eliminar un empleado. El uso de un repositorio ayuda a mantener el código organizado y facilita la reutilización de la lógica de acceso a datos en diferentes partes de la aplicación.
class EmpleadoRepository
{
    // Método para obtener todos los empleados de la base de datos.
    public function getAll()
    {
        return Empleado::all();
    }

    // Método para encontrar un empleado por su ID. Devuelve el empleado si se encuentra, o null si no se encuentra.
    public function findById(int $id)
    {
        return Empleado::find($id);
    }

    // Método para crear un nuevo empleado en la base de datos. Recibe un array de datos y devuelve el empleado creado.
    public function create(array $data)
    {
        return Empleado::create($data);
    }

    // Método para actualizar un empleado existente. Recibe el empleado a actualizar y un array de datos con los nuevos valores. Devuelve el empleado actualizado.
    public function update(Empleado $empleado, array $data)
    {
        $empleado->update($data);

        return $empleado;
    }

    // Método para eliminar un empleado. Recibe el empleado a eliminar y devuelve un booleano indicando si la operación fue exitosa.
    public function delete(Empleado $empleado)
    {
        return $empleado->delete();
    }
}
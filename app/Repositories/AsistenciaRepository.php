<?php

namespace App\Repositories;

use App\Models\Asistencia;

// Repositorio para manejar las operaciones de acceso a datos relacionadas con la asistencia de los empleados. Este repositorio proporciona métodos para obtener todas las asistencias, encontrar una asistencia por su ID, encontrar una jornada activa para un empleado específico, crear una nueva asistencia, actualizar una asistencia existente y eliminar una asistencia. Al centralizar estas operaciones en un repositorio, se mejora la organización del código y se facilita el mantenimiento y la reutilización de la lógica de acceso a datos relacionada con la asistencia.
class AsistenciaRepository
{
    // Obtener todas las asistencias registradas en el sistema. Este método devuelve un arreglo de todas las asistencias, incluyendo detalles como el ID del empleado, la fecha, la hora de entrada, la hora de salida y el estado de cada asistencia.
    public function getAll()
    {
        return Asistencia::all();
    }

    // Encontrar una asistencia por su ID. Este método busca un registro de asistencia utilizando su ID y devuelve la información de la asistencia si se encuentra. Si no se encuentra la asistencia, devuelve null.
    public function findById($id)
    {
        return Asistencia::find($id);
    }

    // Encontrar una jornada activa para un empleado específico. Este método busca una asistencia que corresponda a un empleado específico (identificado por su ID) y que tenga el estado "Activa" sin una hora de salida registrada. Si se encuentra una jornada activa, se devuelve la información de esa asistencia; de lo contrario, devuelve null.
    public function findActiveShift($idEmpleado)
    {
        return Asistencia::where('id_empleado', $idEmpleado)
            ->where('estado', 'Activa')
            ->whereNull('hora_salida')
            ->first();
    }

    // Crear una nueva asistencia. Este método recibe un arreglo de datos que contiene la información necesaria para crear un nuevo registro de asistencia, como el ID del empleado, la fecha, la hora de entrada y el estado. El método utiliza el modelo Asistencia para crear un nuevo registro en la base de datos y devuelve la información del nuevo registro creado.
    public function create(array $data)
    {
        return Asistencia::create($data);
    }

    // Actualizar una asistencia existente. Este método recibe una instancia de Asistencia y un arreglo de datos con la información actualizada. El método actualiza el registro de asistencia con los nuevos datos proporcionados y devuelve la información actualizada de la asistencia.
    public function update(Asistencia $asistencia, array $data)
    {
        $asistencia->update($data);

        return $asistencia;
    }

    // Eliminar una asistencia por ID. Este método recibe una instancia de Asistencia y elimina el registro correspondiente de la base de datos. Si la eliminación es exitosa, devuelve true; de lo contrario, devuelve false.
    public function delete(Asistencia $asistencia)
    {
        return $asistencia->delete();
    }
}
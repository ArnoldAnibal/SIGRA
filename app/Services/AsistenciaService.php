<?php

namespace App\Services;

use App\Repositories\AsistenciaRepository;
use App\Models\Asistencia;

// Servicio para manejar la lógica de negocio relacionada con la asistencia de los empleados. Este servicio utiliza el AsistenciaRepository para interactuar con la base de datos y proporciona métodos para obtener todas las asistencias, obtener una asistencia por su ID, registrar la entrada (clock in) de un empleado, registrar la salida (clock out) de un empleado y eliminar una asistencia. Al centralizar esta lógica en un servicio, se mejora la organización del código y se facilita el mantenimiento y la reutilización de la lógica relacionada con la asistencia.
class AsistenciaService
{
    protected $repository;

    // Constructor para inyectar el AsistenciaRepository en el servicio. Esto permite que el servicio utilice el repositorio para realizar operaciones de acceso a datos relacionadas con la asistencia de los empleados.
    public function __construct(AsistenciaRepository $repository)
    {
        $this->repository = $repository;
    }

    // Obtener todas las asistencias registradas en el sistema. Este método devuelve un arreglo de todas las asistencias, incluyendo detalles como el ID del empleado, la fecha, la hora de entrada, la hora de salida y el estado de cada asistencia.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Obtener una asistencia por su ID. Este método busca un registro de asistencia utilizando su ID y devuelve la información de la asistencia si se encuentra. Si no se encuentra la asistencia, devuelve null.
    public function getById($id)
    {
        return $this->repository->findById($id);
    }

    // Clock In - Registrar la entrada de un empleado. Este método recibe el ID del empleado y verifica si ya tiene una jornada activa (es decir, una asistencia sin hora de salida y con estado "Activa"). Si el empleado ya tiene una jornada activa, se devuelve null. Si no, se crea un nuevo registro de asistencia con la fecha actual, la hora de entrada y el estado "Activa", y se devuelve la información del nuevo registro creado.
    public function clockIn($idEmpleado)
    {
        $activeShift = $this->repository->findActiveShift($idEmpleado);

        if ($activeShift) {
            return null;
        }

        return $this->repository->create([
            'id_empleado' => $idEmpleado,
            'fecha' => now()->toDateString(),
            'hora_entrada' => now(),
            'estado' => 'Activa'
        ]);
    }

    // Clock Out - Registrar la salida de un empleado. Este método recibe el ID de la asistencia y busca el registro correspondiente. Si la asistencia no existe, se devuelve false. Si la asistencia ya ha sido finalizada, se devuelve 'already_closed'. Si la asistencia está activa, se actualiza el registro con la hora de salida y el estado "Finalizada", y se devuelve la información actualizada de la asistencia.
    public function clockOut($id)
    {
        $asistencia = $this->repository->findById($id);

        if (!$asistencia) {
            return false;
        }

        if ($asistencia->estado === 'Finalizada') {
            return 'already_closed';
        }

        return $this->repository->update($asistencia, [
            'hora_salida' => now(),
            'estado' => 'Finalizada'
        ]);
    }

    // Registrar salida usando ID de empleado
public function cerrarJornadaPorEmpleado($id_empleado)
{
    $asistencia = Asistencia::where('id_empleado', $id_empleado)
        ->where('estado', 'Activa')
        ->first();

    if (!$asistencia) {
        return false;
    }

    $asistencia->update([
        'hora_salida' => now(),
        'estado' => 'Finalizada'
    ]);

    return $asistencia;
}

    // Eliminar una asistencia por ID. Este método recibe el ID de la asistencia y busca el registro correspondiente. Si la asistencia no existe, se devuelve false. Si la asistencia existe, se elimina el registro y se devuelve true si la eliminación es exitosa; de lo contrario, devuelve false.
    public function delete($id)
    {
        $asistencia = $this->repository->findById($id);

        if (!$asistencia) {
            return false;
        }

        return $this->repository->delete($asistencia);
    }
    

}
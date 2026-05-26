<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsistenciaRequest;
use App\Http\Requests\UpdateAsistenciaRequest;
use App\Services\AsistenciaService;

// Controlador para manejar las solicitudes relacionadas con asistencia.
// Utiliza AsistenciaService para encapsular toda la lógica de negocio.
class AsistenciaController extends Controller
{
    protected $service;

    // Inyección del servicio
    public function __construct(AsistenciaService $service)
    {
        $this->service = $service;
    }

    // Listar todas las asistencias
    public function index()
    {
        return response()->json(
            $this->service->getAll()
        );
    }

    // Registrar entrada (Clock In)
    public function store(StoreAsistenciaRequest $request)
    {
        $asistencia = $this->service->clockIn(
            $request->id_empleado
        );

        if (!$asistencia) {

            return response()->json([
                'message' => 'El empleado ya tiene una jornada activa.'
            ], 400);
        }

        return response()->json($asistencia, 201);
    }

    // Mostrar asistencia por ID
    public function show($id)
    {
        $asistencia = $this->service->getById($id);

        if (!$asistencia) {

            return response()->json([
                'message' => 'Asistencia no encontrada'
            ], 404);
        }

        return response()->json($asistencia);
    }

    // Registrar salida usando ID de asistencia
    public function update(UpdateAsistenciaRequest $request, $id)
    {
        $result = $this->service->clockOut($id);

        if ($result === false) {

            return response()->json([
                'message' => 'Asistencia no encontrada'
            ], 404);
        }

        if ($result === 'already_closed') {

            return response()->json([
                'message' => 'La jornada ya fue finalizada.'
            ], 400);
        }

        return response()->json($result);
    }

    // Registrar salida usando ID de empleado
    public function cerrarJornada($id_empleado)
    {
        $asistencia = $this->service
            ->cerrarJornadaPorEmpleado($id_empleado);

        if (!$asistencia) {

            return response()->json([
                'message' => 'No existe una jornada activa para este empleado.'
            ], 404);
        }

        return response()->json([
            'message' => 'Salida registrada correctamente.',
            'data' => $asistencia
        ]);
    }

    // Eliminar asistencia
    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {

            return response()->json([
                'message' => 'Asistencia no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Asistencia eliminada correctamente'
        ]);
    }
}
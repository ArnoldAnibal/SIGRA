<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsistenciaRequest;
use App\Http\Requests\UpdateAsistenciaRequest;
use App\Services\AsistenciaService;
use Illuminate\Http\Request;

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

    /*
    |--------------------------------------------------------------------------
    | CLOCK IN
    |--------------------------------------------------------------------------
    |
    | El empleado se obtiene automáticamente desde el token Sanctum.
    | Ya NO se envía id_empleado desde el frontend.
    |
    */

    public function store(
        StoreAsistenciaRequest $request
    ) {

        $empleado = $request->user();

        $asistencia = $this->service->clockIn(
            $empleado->id_empleado
        );

        if (!$asistencia) {

            return response()->json([
                'message' => 'El empleado ya tiene una jornada activa.'
            ], 400);
        }

        return response()->json([
            'message' => 'Entrada registrada correctamente.',
            'data' => $asistencia
        ], 201);
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

    /*
    |--------------------------------------------------------------------------
    | CLOCK OUT POR ID ASISTENCIA
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateAsistenciaRequest $request,
        $id
    ) {

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

        return response()->json([
            'message' => 'Salida registrada correctamente.',
            'data' => $result
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CLOCK OUT AUTOMÁTICO POR TOKEN
    |--------------------------------------------------------------------------
    |
    | Ya NO recibe id_empleado.
    | El empleado se obtiene automáticamente desde Sanctum.
    |
    */

    public function cerrarJornada(Request $request)
{
    $id_empleado = $request->user()->id_empleado;

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
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanillaRequest;
use App\Http\Requests\UpdatePlanillaRequest;
use App\Http\Resources\PlanillaResource;
use App\Services\PlanillaService;

// Controlador para manejar las solicitudes relacionadas con las planillas en la API. Este controlador utiliza el PlanillaService para realizar las operaciones de negocio relacionadas con las planillas, como obtener todas las planillas, obtener una planilla por su ID, crear una nueva planilla, actualizar una planilla existente y eliminar una planilla. El controlador también utiliza PlanillaResource para transformar los datos de las planillas antes de devolverlos en las respuestas de la API. Además, el controlador utiliza StorePlanillaRequest y UpdatePlanillaRequest para validar los datos enviados al crear o actualizar una planilla, respectivamente.
class PlanillaController extends Controller
{
    protected $service;

    // Constructor que recibe una instancia de PlanillaService y la asigna a la propiedad $service. Esto permite que el controlador utilice el servicio para realizar las operaciones relacionadas con las planillas.
    public function __construct(PlanillaService $service)
    {
        $this->service = $service;
    }

    // Método para obtener todas las planillas. Utiliza el método getAll del servicio para obtener los datos de las planillas, los transforma utilizando PlanillaResource y devuelve una respuesta JSON con los datos de las planillas.
    public function index()
    {
        return PlanillaResource::collection(
            $this->service->getAll()
        );
    }

    // Método para crear una nueva planilla. Utiliza el método create del servicio para crear la planilla, lo transforma utilizando PlanillaResource y devuelve una respuesta JSON con los datos de la planilla creada.
    public function store(StorePlanillaRequest $request)
    {
        $planilla = $this->service->create(
            $request->validated()
        );

        return new PlanillaResource($planilla);
    }

    // Método para obtener una planilla por su ID. Utiliza el método getById del servicio para obtener la planilla correspondiente al ID proporcionado, lo transforma utilizando PlanillaResource y devuelve una respuesta JSON con los datos de la planilla. Si la planilla no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function show($id)
    {
        $planilla = $this->service->getById($id);

        if (!$planilla) {
            return response()->json([
                'message' => 'Planilla no encontrada'
            ], 404);
        }

        return new PlanillaResource($planilla);
    }

    // Método para actualizar una planilla existente. Utiliza el método getById del servicio para obtener la planilla correspondiente al ID proporcionado, lo transforma utilizando PlanillaResource y devuelve una respuesta JSON con los datos de la planilla actualizado. Si la planilla no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function update(UpdatePlanillaRequest $request, $id)
    {
        $planilla = $this->service->getById($id);

        if (!$planilla) {
            return response()->json([
                'message' => 'Planilla no encontrada'
            ], 404);
        }

        $planillaActualizada = $this->service->update(
            $planilla,
            $request->validated()
        );

        return new PlanillaResource($planillaActualizada);
    }

    // Método para eliminar una planilla. Utiliza el método getById del servicio para obtener la planilla correspondiente al ID proporcionado. Si la planilla no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si la planilla se encuentra, utiliza el método delete del servicio para eliminar la planilla y devuelve una respuesta JSON con un mensaje de éxito.
    public function destroy($id)
    {
        $planilla = $this->service->getById($id);

        if (!$planilla) {
            return response()->json([
                'message' => 'Planilla no encontrada'
            ], 404);
        }

        $this->service->delete($planilla);

        return response()->json([
            'message' => 'Planilla eliminada correctamente'
        ]);
    }
}
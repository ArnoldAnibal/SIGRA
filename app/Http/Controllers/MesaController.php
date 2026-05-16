<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMesaRequest;
use App\Http\Requests\UpdateMesaRequest;
use App\Http\Resources\MesaResource;
use App\Services\MesaService;

class MesaController extends Controller
{
    protected $service;

    public function __construct(MesaService $service)
    {
        $this->service = $service;
    }

    // Obtener todas las mesas
    public function index()
    {
        return response()->json([
            'data' => $this->service->getAll()
        ]);
    }

    // Crear mesa
    public function store(StoreMesaRequest $request)
    {
        $mesa = $this->service->create($request->validated());

        return new MesaResource($mesa);
    }

    // Obtener mesa por ID
    public function show($id)
    {
        $mesa = $this->service->getById($id);

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa no encontrada'
            ], 404);
        }

        return new MesaResource($mesa);
    }

    // Actualizar mesa
    public function update(UpdateMesaRequest $request, $id)
    {
        $mesa = $this->service->getById($id);

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa no encontrada'
            ], 404);
        }

        $mesaActualizada = $this->service->update(
            $mesa,
            $request->validated()
        );

        return new MesaResource($mesaActualizada);
    }

    // Eliminar mesa
    public function destroy($id)
    {
        $mesa = $this->service->getById($id);

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa no encontrada'
            ], 404);
        }

        $this->service->delete($mesa);

        return response()->json([
            'message' => 'Mesa eliminada correctamente'
        ]);
    }
}
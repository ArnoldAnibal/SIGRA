<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventarioMovimientoRequest;
use App\Http\Requests\UpdateInventarioMovimientoRequest;
use App\Http\Resources\InventarioMovimientoResource;
use App\Services\InventarioMovimientoService;

class InventarioMovimientoController extends Controller
{
    protected $service;

    public function __construct(
        InventarioMovimientoService $service
    ) {
        $this->service = $service;
    }

    public function index()
    {
        return InventarioMovimientoResource::collection(
            $this->service->getAll()
        );
    }

    public function store(
        StoreInventarioMovimientoRequest $request
    ) {
        $movimiento =
            $this->service->create(
                $request->validated()
            );

        return new InventarioMovimientoResource(
            $movimiento
        );
    }

    public function show(string $id)
    {
        $movimiento =
            $this->service->getById($id);

        if (!$movimiento) {

            return response()->json([
                'message' =>
                    'Movimiento no encontrado'
            ], 404);
        }

        return new InventarioMovimientoResource(
            $movimiento
        );
    }

    public function update(
        UpdateInventarioMovimientoRequest $request,
        string $id
    ) {
        $movimiento =
            $this->service->getById($id);

        if (!$movimiento) {

            return response()->json([
                'message' =>
                    'Movimiento no encontrado'
            ], 404);
        }

        $movimientoActualizado =
            $this->service->update(
                $movimiento,
                $request->validated()
            );

        return new InventarioMovimientoResource(
            $movimientoActualizado
        );
    }

    public function destroy(string $id)
    {
        $movimiento =
            $this->service->getById($id);

        if (!$movimiento) {

            return response()->json([
                'message' =>
                    'Movimiento no encontrado'
            ], 404);
        }

        $this->service->delete(
            $movimiento
        );

        return response()->json([
            'message' =>
                'Movimiento eliminado correctamente'
        ]);
    }
}
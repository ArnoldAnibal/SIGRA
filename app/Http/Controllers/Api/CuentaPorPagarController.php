<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaPorPagarRequest;
use App\Http\Requests\UpdateCuentaPorPagarRequest;
use App\Http\Resources\CuentaPorPagarResource;
use App\Services\CuentaPorPagarService;

class CuentaPorPagarController extends Controller
{
    protected $service;

    public function __construct(
        CuentaPorPagarService $service
    ) {
        $this->service = $service;
    }

    public function index()
    {
        return CuentaPorPagarResource::collection(
            $this->service->getAll()
        );
    }

    public function store(
        StoreCuentaPorPagarRequest $request
    ) {
        $cuenta = $this->service->create(
            $request->validated()
        );

        return new CuentaPorPagarResource(
            $cuenta
        );
    }

    public function show(string $id)
    {
        $cuenta =
            $this->service->getById($id);

        if (!$cuenta) {

            return response()->json([
                'message' =>
                    'Cuenta por pagar no encontrada'
            ], 404);
        }

        return new CuentaPorPagarResource(
            $cuenta
        );
    }

    public function update(
        UpdateCuentaPorPagarRequest $request,
        string $id
    ) {
        $cuenta =
            $this->service->getById($id);

        if (!$cuenta) {

            return response()->json([
                'message' =>
                    'Cuenta por pagar no encontrada'
            ], 404);
        }

        $cuentaActualizada =
            $this->service->update(
                $cuenta,
                $request->validated()
            );

        return new CuentaPorPagarResource(
            $cuentaActualizada
        );
    }

    public function destroy(string $id)
    {
        $cuenta =
            $this->service->getById($id);

        if (!$cuenta) {

            return response()->json([
                'message' =>
                    'Cuenta por pagar no encontrada'
            ], 404);
        }

        $this->service->delete($cuenta);

        return response()->json([
            'message' =>
                'Cuenta por pagar eliminada correctamente'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagoRequest;
use App\Http\Requests\UpdatePagoRequest;
use App\Http\Resources\PagoResource;
use App\Services\PagoService;

class PagoController extends Controller
{
    protected $service;

    public function __construct(PagoService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return PagoResource::collection(
            $this->service->getAll()
        );
    }

    public function store(StorePagoRequest $request)
    {
        $pago = $this->service->create(
            $request->validated()
        );

        return new PagoResource($pago);
    }

    public function show($id)
    {
        $pago = $this->service->getById($id);

        if (!$pago) {

            return response()->json([
                'message' => 'Pago no encontrado'
            ], 404);
        }

        return new PagoResource($pago);
    }

    public function update(
        UpdatePagoRequest $request,
        $id
    ) {

        $pago = $this->service->getById($id);

        if (!$pago) {

            return response()->json([
                'message' => 'Pago no encontrado'
            ], 404);
        }

        $pagoActualizado =
            $this->service->update(
                $pago,
                $request->validated()
            );

        return new PagoResource(
            $pagoActualizado
        );
    }

    public function destroy($id)
    {
        $pago = $this->service->getById($id);

        if (!$pago) {

            return response()->json([
                'message' => 'Pago no encontrado'
            ], 404);
        }

        $this->service->delete($pago);

        return response()->json([
            'message' =>
                'Pago eliminado correctamente'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagoRequest;
use App\Http\Requests\UpdatePagoRequest;
use App\Http\Resources\PagoResource;
use App\Services\PagoService;

// Controlador para gestionar los pagos. Este controlador maneja las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para los pagos en la aplicación. Utiliza el servicio PagoService para interactuar con la lógica de negocio relacionada con los pagos y los recursos PagoResource para transformar los datos del modelo Pago en un formato adecuado para las respuestas de la API. El controlador también utiliza StorePagoRequest y UpdatePagoRequest para validar los datos enviados al crear o actualizar un pago, respectivamente. 
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
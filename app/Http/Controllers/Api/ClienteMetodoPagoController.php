<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteMetodoPagoRequest;
use App\Http\Requests\UpdateClienteMetodoPagoRequest;
use App\Http\Resources\ClienteMetodoPagoResource;
use App\Services\ClienteMetodoPagoService;

// Controlador para gestionar los métodos de pago de los clientes
class ClienteMetodoPagoController extends Controller
{
    protected $service;

    public function __construct(
        ClienteMetodoPagoService $service
    ) {
        $this->service = $service;
    }

    public function index()
    {
        return ClienteMetodoPagoResource::collection(
            $this->service->getAll()
        );
    }

    public function store(
        StoreClienteMetodoPagoRequest $request
    ) {
        $metodo = $this->service->create(
            $request->validated()
        );

        return new ClienteMetodoPagoResource(
            $metodo
        );
    }

    public function show(string $id)
    {
        $metodo = $this->service->getById($id);

        if (!$metodo) {

            return response()->json([
                'message' =>
                    'Método de pago no encontrado'
            ], 404);
        }

        return new ClienteMetodoPagoResource(
            $metodo
        );
    }

    public function update(
        UpdateClienteMetodoPagoRequest $request,
        string $id
    ) {
        $metodo = $this->service->getById($id);

        if (!$metodo) {

            return response()->json([
                'message' =>
                    'Método de pago no encontrado'
            ], 404);
        }

        $metodoActualizado =
            $this->service->update(
                $metodo,
                $request->validated()
            );

        return new ClienteMetodoPagoResource(
            $metodoActualizado
        );
    }

    public function destroy(string $id)
    {
        $metodo = $this->service->getById($id);

        if (!$metodo) {

            return response()->json([
                'message' =>
                    'Método de pago no encontrado'
            ], 404);
        }

        $this->service->delete($metodo);

        return response()->json([
            'message' =>
                'Método de pago eliminado correctamente'
        ]);
    }
}
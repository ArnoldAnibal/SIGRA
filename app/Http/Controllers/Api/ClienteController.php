<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Services\ClienteService;

// Controlador para gestionar los clientes en la aplicación. Este controlador maneja las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para los clientes en la aplicación. Utiliza el servicio ClienteService para interactuar con la lógica de negocio relacionada con los clientes y los recursos ClienteResource para transformar los datos del modelo Cliente en un formato adecuado para las respuestas de la API. El controlador también utiliza StoreClienteRequest y UpdateClienteRequest para validar los datos enviados al crear o actualizar un cliente, respectivamente.
class ClienteController extends Controller
{
    protected $service;

    public function __construct(ClienteService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'data' => ClienteResource::collection($this->service->getAll())
        ]);
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = $this->service->create($request->validated());

        return new ClienteResource($cliente);
    }

    public function show($id)
    {
        $cliente = $this->service->findById($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return new ClienteResource($cliente);
    }

    public function update(UpdateClienteRequest $request, $id)
    {
        $cliente = $this->service->update($id, $request->validated());

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return new ClienteResource($cliente);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Cliente eliminado correctamente'
        ]);
    }
}
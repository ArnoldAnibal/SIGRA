<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Services\ClienteService;


// Controlador para manejar las solicitudes relacionadas con los clientes en la API. Este controlador utiliza el ClienteService para realizar las operaciones de negocio relacionadas con los clientes, como obtener todos los clientes, obtener un cliente por su ID, crear un nuevo cliente, actualizar un cliente existente y eliminar un cliente. El controlador también utiliza ClienteResource para transformar los datos de los clientes antes de devolverlos en las respuestas de la API. Además, el controlador utiliza StoreClienteRequest y UpdateClienteRequest para validar los datos enviados al crear o actualizar un cliente, respectivamente.
class ClienteController extends Controller
{
    protected $service;

    // Constructor que recibe una instancia de ClienteService y la asigna a la propiedad $service. Esto permite que el controlador utilice el servicio para realizar las operaciones relacionadas con los clientes.
    public function __construct(ClienteService $service)
    {
        $this->service = $service;
    }

    // Método para obtener todos los clientes. Utiliza el método getAll del servicio para obtener los datos de los clientes, los transforma utilizando ClienteResource y devuelve una respuesta JSON con los datos de los clientes.
    public function index()
    {
        return response()->json([
            'data' => $this->service->getAll()
        ]);
    }

    // Método para crear un nuevo cliente. Utiliza el método create del servicio para crear el cliente, lo transforma utilizando ClienteResource y devuelve una respuesta JSON con los datos del cliente creado.
    public function store(StoreClienteRequest $request)
    {
        $cliente = $this->service->create($request->validated());

        return new ClienteResource($cliente);
    }

    // Método para obtener un cliente por su ID. Utiliza el método getById del servicio para obtener el cliente correspondiente al ID proporcionado, lo transforma utilizando ClienteResource y devuelve una respuesta JSON con los datos del cliente. Si el cliente no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function show($id)
    {
        $cliente = $this->service->getById($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return new ClienteResource($cliente);
    }

    // Método para actualizar un cliente existente. Utiliza el método getById del servicio para obtener el cliente correspondiente al ID proporcionado, lo transforma utilizando ClienteResource y devuelve una respuesta JSON con los datos del cliente actualizado. Si el cliente no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function update(UpdateClienteRequest $request, $id)
    {
        // Utiliza el método getById del servicio para obtener el cliente correspondiente al ID proporcionado. Si el cliente no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
        $cliente = $this->service->getById($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        // Utiliza el método update del servicio para actualizar el cliente, lo transforma utilizando ClienteResource y devuelve una respuesta JSON con los datos del cliente actualizado.
        $clienteActualizado = $this->service->update(
            $cliente,
            $request->validated()
        );

        return new ClienteResource($clienteActualizado);
    }

    // Método para eliminar un cliente. Utiliza el método getById del servicio para obtener el cliente correspondiente al ID proporcionado, lo transforma utilizando ClienteResource y devuelve una respuesta JSON con los datos del cliente eliminado. Si el cliente no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el cliente se elimina correctamente, devuelve una respuesta JSON con un mensaje de éxito.
    public function destroy($id)
    {
        $cliente = $this->service->getById($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        $this->service->delete($cliente);

        return response()->json([
            'message' => 'Cliente eliminado correctamente'
        ]);
    }
}
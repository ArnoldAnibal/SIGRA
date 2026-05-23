<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePedidoRequest;
use App\Http\Requests\UpdatePedidoRequest;
use App\Http\Resources\PedidoResource;
use App\Services\PedidoService;

// Controlador para manejar las solicitudes relacionadas con los pedidos en la API. Este controlador utiliza el PedidoService para realizar las operaciones de negocio relacionadas con los pedidos, como obtener todos los pedidos, obtener un pedido por su ID, crear un nuevo pedido, actualizar un pedido existente y eliminar un pedido. El controlador también utiliza PedidoResource para transformar los datos de los pedidos antes de devolverlos en las respuestas de la API. Además, el controlador utiliza StorePedidoRequest y UpdatePedidoRequest para validar los datos enviados al crear o actualizar un pedido, respectivamente.
class PedidoController extends Controller
{
    protected $service;

    // Constructor que recibe una instancia de PedidoService y la asigna a la propiedad $service. Esto permite que el controlador utilice el servicio para realizar las operaciones relacionadas con los pedidos.
    public function __construct(PedidoService $service)
    {
        $this->service = $service;
    }

     // Método para obtener todos los pedidos. Utiliza el método getAll del servicio para obtener los datos de los pedidos, los transforma utilizando PedidoResource y devuelve una respuesta JSON con los datos de los pedidos.
    public function index()
    {
        return PedidoResource::collection(
            $this->service->getAll()
        );
    }

    // Método para crear un nuevo pedido. Utiliza el método create del servicio para crear el pedido, lo transforma utilizando PedidoResource y devuelve una respuesta JSON con los datos del pedido creado.
    public function store(StorePedidoRequest $request)
    {
        $pedido = $this->service->create(
            $request->validated()
        );

        return new PedidoResource($pedido);
    }

    // Método para obtener un pedido por su ID. Utiliza el método getById del servicio para obtener el pedido correspondiente al ID proporcionado, lo transforma utilizando PedidoResource y devuelve una respuesta JSON con los datos del pedido. Si el pedido no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function show($id)
    {
        $pedido = $this->service->getById($id);

        if (!$pedido) {
            return response()->json([
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        return new PedidoResource($pedido);
    }

    // Método para actualizar un pedido existente. Utiliza el método getById del servicio para obtener el pedido correspondiente al ID proporcionado, lo transforma utilizando PedidoResource y devuelve una respuesta JSON con los datos del pedido actualizado. Si el pedido no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function update(UpdatePedidoRequest $request, $id)
    {
        $pedido = $this->service->getById($id);

        if (!$pedido) {
            return response()->json([
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        $pedidoActualizado = $this->service->update(
            $pedido,
            $request->validated()
        );

        return new PedidoResource($pedidoActualizado);
    }

    // Método para eliminar un pedido. Utiliza el método getById del servicio para obtener el pedido correspondiente al ID proporcionado. Si el pedido no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el pedido se encuentra, utiliza el método delete del servicio para eliminar el pedido y devuelve una respuesta JSON con un mensaje de éxito.
    public function destroy($id)
    {
        $pedido = $this->service->getById($id);

        if (!$pedido) {
            return response()->json([
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        $this->service->delete($pedido);

        return response()->json([
            'message' => 'Pedido eliminado correctamente'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDetallePedidoRequest;
use App\Http\Requests\UpdateDetallePedidoRequest;
use App\Http\Resources\DetallePedidoResource;
use App\Services\DetallePedidoService;

// Controlador para manejar las solicitudes relacionadas con los detalles de pedido en la API. Este controlador utiliza el DetallePedidoService para realizar las operaciones de negocio relacionadas con los detalles de pedido, como obtener todos los detalles de pedido, obtener un detalle de pedido por su ID, crear un nuevo detalle de pedido, actualizar un detalle de pedido existente y eliminar un detalle de pedido. El controlador también utiliza DetallePedidoResource para transformar los datos de los detalles de pedido antes de devolverlos en las respuestas de la API. Además, el controlador utiliza StoreDetallePedidoRequest y UpdateDetallePedidoRequest para validar los datos enviados al crear o actualizar un detalle de pedido, respectivamente.

class DetallePedidoController extends Controller
{
    protected $service;

    // Constructor que recibe una instancia de DetallePedidoService y la asigna a la propiedad $service. Esto permite que el controlador utilice el servicio para realizar las operaciones relacionadas con los detalles de pedido.
    public function __construct(DetallePedidoService $service)
    {
        $this->service = $service;
    }

    // Método para obtener todos los detalles de pedido. Utiliza el método getAll del servicio para obtener los datos de los detalles de pedido, los transforma utilizando DetallePedidoResource y devuelve una respuesta JSON con los datos de los detalles de pedido.
    public function index()
    {
        return DetallePedidoResource::collection(
            $this->service->getAll()
        );
    }

    // Método para crear un nuevo detalle de pedido. Utiliza el método create del servicio para crear el detalle de pedido, lo transforma utilizando DetallePedidoResource y devuelve una respuesta JSON con los datos del detalle de pedido creado.
    public function store(StoreDetallePedidoRequest $request)
    {
        $detalle = $this->service->create(
            $request->validated()
        );

        return new DetallePedidoResource($detalle);
    }

    // Método para obtener un detalle de pedido por su ID. Utiliza el método getById del servicio para obtener el detalle de pedido correspondiente al ID proporcionado, lo transforma utilizando DetallePedidoResource y devuelve una respuesta JSON con los datos del detalle de pedido. Si el detalle de pedido no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function show($id)
    {
        $detalle = $this->service->getById($id);

        if (!$detalle) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }

        return new DetallePedidoResource($detalle);
    }

    //  Método para actualizar un detalle de pedido existente. Utiliza el método getById del servicio para obtener el detalle de pedido correspondiente al ID proporcionado, lo transforma utilizando DetallePedidoResource y devuelve una respuesta JSON con los datos del detalle de pedido actualizado. Si el detalle de pedido no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function update(UpdateDetallePedidoRequest $request, $id)
    {
        $detalle = $this->service->getById($id);

        if (!$detalle) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }

        $detalleActualizado = $this->service->update(
            $detalle,
            $request->validated()
        );

        return new DetallePedidoResource($detalleActualizado);
    }

    // Método para eliminar un detalle de pedido. Utiliza el método getById del servicio para obtener el detalle de pedido correspondiente al ID proporcionado, lo transforma utilizando DetallePedidoResource y devuelve una respuesta JSON con los datos del detalle de pedido eliminado. Si el detalle de pedido no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el detalle de pedido se elimina correctamente, devuelve una respuesta JSON con un mensaje de éxito.
    public function destroy($id)
    {
        $detalle = $this->service->getById($id);

        if (!$detalle) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }

        $this->service->delete($detalle);

        return response()->json([
            'message' => 'Detalle eliminado correctamente'
        ]);
    }
}
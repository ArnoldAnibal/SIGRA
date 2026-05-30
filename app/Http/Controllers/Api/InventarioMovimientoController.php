<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventarioMovimientoRequest;
use App\Http\Requests\UpdateInventarioMovimientoRequest;
use App\Http\Resources\InventarioMovimientoResource;
use App\Services\InventarioMovimientoService;

// Controlador para gestionar los movimientos de inventario. Este controlador maneja las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para los movimientos de inventario en la aplicación. Utiliza el servicio InventarioMovimientoService para interactuar con la lógica de negocio relacionada con los movimientos de inventario y los recursos InventarioMovimientoResource para transformar los datos del modelo InventarioMovimiento en un formato adecuado para las respuestas de la API. El controlador también utiliza StoreInventarioMovimientoRequest y UpdateInventarioMovimientoRequest para validar los datos enviados al crear o actualizar un movimiento de inventario, respectivamente.
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

    // Método para obtener los detalles de un movimiento de inventario específico. Este método recibe el ID del movimiento de inventario como parámetro y utiliza el servicio InventarioMovimientoService para obtener los datos del movimiento de inventario correspondiente. Si el movimiento de inventario no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el movimiento de inventario se encuentra, se devuelve un recurso InventarioMovimientoResource con los datos del movimiento de inventario. Al llamar a este método con un ID de movimiento de inventario válido, se obtiene una respuesta JSON con los detalles del movimiento de inventario solicitado.
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

    // Método para actualizar los datos de un movimiento de inventario existente. Este método recibe una solicitud de tipo UpdateInventarioMovimientoRequest, que valida los datos enviados en la solicitud para actualizar un movimiento de inventario. Además, recibe el ID del movimiento de inventario a actualizar como parámetro. El método utiliza el servicio InventarioMovimientoService para obtener los datos del movimiento de inventario correspondiente. Si el movimiento de inventario no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el movimiento de inventario se encuentra, se utiliza el servicio para actualizar los datos del movimiento de inventario con los datos validados y luego se devuelve un recurso InventarioMovimientoResource con los datos del movimiento de inventario actualizado. Al llamar a este método con un ID de movimiento de inventario válido y datos de actualización válidos, se obtiene una respuesta JSON con los detalles del movimiento de inventario actualizado en la aplicación.
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

    // Método para eliminar un movimiento de inventario existente. Este método recibe el ID del movimiento de inventario como parámetro y utiliza el servicio InventarioMovimientoService para obtener los datos del movimiento de inventario correspondiente. Si el movimiento de inventario no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el movimiento de inventario se encuentra, se utiliza el servicio para eliminar el movimiento de inventario y luego se devuelve una respuesta JSON con un mensaje de éxito. Al llamar a este método con un ID de movimiento de inventario válido, se obtiene una respuesta JSON con el mensaje de éxito indicando que el movimiento de inventario ha sido eliminado correctamente en la aplicación.
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
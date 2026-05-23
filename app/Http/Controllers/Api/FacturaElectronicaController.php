<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacturaElectronicaRequest;
use App\Http\Requests\UpdateFacturaElectronicaRequest;
use App\Http\Resources\FacturaElectronicaResource;
use App\Services\FacturaElectronicaService;

// Controlador para manejar las solicitudes relacionadas con las facturas electrónicas en la API. Este controlador utiliza el FacturaElectronicaService para realizar las operaciones de negocio relacionadas con las facturas electrónicas, como obtener todas las facturas, obtener una factura por su ID, crear una nueva factura, actualizar una factura existente y eliminar una factura. El controlador también utiliza FacturaElectronicaResource para transformar los datos de las facturas electrónicas antes de devolverlos en las respuestas de la API. Además, el controlador utiliza StoreFacturaElectronicaRequest y UpdateFacturaElectronicaRequest para validar los datos enviados al crear o actualizar una factura electrónica, respectivamente.
class FacturaElectronicaController extends Controller
{
    protected $service;

    // Constructor que recibe una instancia de FacturaElectronicaService y la asigna a la propiedad $service. Esto permite que el controlador utilice el servicio para realizar las operaciones relacionadas con las facturas electrónicas.
    public function __construct(FacturaElectronicaService $service)
    {
        $this->service = $service;
    }

    // Método para obtener todas las facturas electrónicas. Utiliza el método getAll del servicio para obtener los datos de las facturas electrónicas, los transforma utilizando FacturaElectronicaResource y devuelve una respuesta JSON con los datos de las facturas electrónicas.
    public function index()
    {
        return FacturaElectronicaResource::collection(
            $this->service->getAll()
        );
    }

    // Método para crear una nueva factura electrónica. Utiliza el método create del servicio para crear la factura, lo transforma utilizando FacturaElectronicaResource y devuelve una respuesta JSON con los datos de la factura creada.
    public function store(StoreFacturaElectronicaRequest $request)
    {
        $factura = $this->service->create(
            $request->validated()
        );

        return new FacturaElectronicaResource($factura);
    }

    // Método para obtener una factura electrónica por su ID. Utiliza el método getById del servicio para obtener la factura correspondiente al ID proporcionado, lo transforma utilizando FacturaElectronicaResource y devuelve una respuesta JSON con los datos de la factura. Si la factura no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function show($id)
    {
        $factura = $this->service->getById($id);

        if (!$factura) {
            return response()->json([
                'message' => 'Factura no encontrada'
            ], 404);
        }

        return new FacturaElectronicaResource($factura);
    }

    // Método para actualizar una factura electrónica existente. Utiliza el método getById del servicio para obtener la factura correspondiente al ID proporcionado, lo transforma utilizando FacturaElectronicaResource y devuelve una respuesta JSON con los datos de la factura actualizada. Si la factura no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function update(UpdateFacturaElectronicaRequest $request, $id)
    {
        $factura = $this->service->getById($id);

        if (!$factura) {
            return response()->json([
                'message' => 'Factura no encontrada'
            ], 404);
        }

        $facturaActualizada = $this->service->update(
            $factura,
            $request->validated()
        );

        return new FacturaElectronicaResource($facturaActualizada);
    }

    // Método para eliminar una factura electrónica. Utiliza el método getById del servicio para obtener la factura correspondiente al ID proporcionado. Si la factura no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si la factura se encuentra, utiliza el método delete del servicio para eliminar la factura y devuelve una respuesta JSON con un mensaje indicando que la factura fue eliminada correctamente.
    public function destroy($id)
    {
        $factura = $this->service->getById($id);

        if (!$factura) {
            return response()->json([
                'message' => 'Factura no encontrada'
            ], 404);
        }

        $this->service->delete($factura);

        return response()->json([
            'message' => 'Factura eliminada correctamente'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuentaPorPagarRequest;
use App\Http\Requests\UpdateCuentaPorPagarRequest;
use App\Http\Resources\CuentaPorPagarResource;
use App\Services\CuentaPorPagarService;


// Controlador para gestionar las cuentas por pagar. Este controlador maneja las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para las cuentas por pagar en la aplicación. Utiliza el servicio CuentaPorPagarService para interactuar con la lógica de negocio relacionada con las cuentas por pagar y los recursos CuentaPorPagarResource para transformar los datos del modelo CuentaPorPagar en un formato adecuado para las respuestas de la API. El controlador también utiliza StoreCuentaPorPagarRequest y UpdateCuentaPorPagarRequest para validar los datos enviados al crear o actualizar una cuenta por pagar, respectivamente.
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

    // Método para obtener los detalles de una cuenta por pagar específica. Este método recibe el ID de la cuenta por pagar como parámetro y utiliza el servicio CuentaPorPagarService para obtener los datos de la cuenta por pagar correspondiente. Si la cuenta por pagar no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si la cuenta por pagar se encuentra, se devuelve un recurso CuentaPorPagarResource con los datos de la cuenta por pagar. Al llamar a este método con un ID de cuenta por pagar válido, se obtiene una respuesta JSON con los detalles de la cuenta por pagar solicitada.
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

    // Método para actualizar los datos de una cuenta por pagar existente. Este método recibe una solicitud de tipo UpdateCuentaPorPagarRequest, que valida los datos enviados en la solicitud para actualizar una cuenta por pagar. Además, recibe el ID de la cuenta por pagar a actualizar como parámetro. El método utiliza el servicio CuentaPorPagarService para obtener los datos de la cuenta por pagar correspondiente. Si la cuenta por pagar no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si la cuenta por pagar se encuentra, se utiliza el servicio para actualizar los datos de la cuenta por pagar con los datos validados y luego se devuelve un recurso CuentaPorPagarResource con los datos de la cuenta por pagar actualizado. Al llamar a este método con un ID de cuenta por pagar válido y datos de actualización válidos, se obtiene una respuesta JSON con los detalles de la cuenta por pagar actualizado en la aplicación.
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

    // Método para eliminar una cuenta por pagar existente. Este método recibe el ID de la cuenta por pagar a eliminar como parámetro. El método utiliza el servicio CuentaPorPagarService para obtener los datos de la cuenta por pagar correspondiente. Si la cuenta por pagar no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si la cuenta por pagar se encuentra, se utiliza el servicio para eliminar la cuenta por pagar y luego se devuelve una respuesta JSON con un mensaje de éxito indicando que la cuenta por pagar ha sido eliminada correctamente. Al llamar a este método con un ID de cuenta por pagar válido, se obtiene una respuesta JSON confirmando la eliminación de la cuenta por pagar en la aplicación.
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
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Http\Resources\ProveedorResource;
use App\Services\ProveedorService;


// Controlador para gestionar los proveedores. Este controlador maneja las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para los proveedores en la aplicación. Utiliza el servicio ProveedorService para interactuar con la lógica de negocio relacionada con los proveedores y los recursos ProveedorResource para transformar los datos del modelo Proveedor en un formato adecuado para las respuestas de la API.
class ProveedorController extends Controller
{
    protected $service;

    // Inyección de dependencias del servicio ProveedorService. El constructor del controlador recibe una instancia de ProveedorService, que se asigna a la propiedad $service. Esto permite que el controlador utilice los métodos definidos en el servicio para realizar las operaciones relacionadas con los proveedores, como obtener todos los proveedores, crear un nuevo proveedor, actualizar un proveedor existente o eliminar un proveedor. Al utilizar la inyección de dependencias, se promueve una arquitectura más modular y facilita la prueba y el mantenimiento del código.
    public function __construct(
        ProveedorService $service
    ) {
        $this->service = $service;
    }

    // Método para obtener una lista de todos los proveedores. Este método utiliza el servicio ProveedorService para obtener todos los proveedores y luego devuelve una colección de recursos ProveedorResource, que transforman los datos del modelo Proveedor en un formato adecuado para las respuestas de la API. Al llamar a este método, se obtiene una respuesta JSON con la lista de proveedores disponibles en la aplicación.
    public function index()
    {
        return ProveedorResource::collection(
            $this->service->getAll()
        );
    }

    // Método para crear un nuevo proveedor. Este método recibe una solicitud de tipo StoreProveedorRequest, que valida los datos enviados en la solicitud para crear un nuevo proveedor. Si la validación es exitosa, el método utiliza el servicio ProveedorService para crear un nuevo proveedor con los datos validados y luego devuelve un recurso ProveedorResource con los datos del proveedor recién creado. Al llamar a este método, se obtiene una respuesta JSON con los detalles del nuevo proveedor creado en la aplicación.
    public function store(
        StoreProveedorRequest $request
    ) {
        $proveedor = $this->service->create(
            $request->validated()
        );

        return new ProveedorResource(
            $proveedor
        );
    }

    // Método para obtener los detalles de un proveedor específico. Este método recibe el ID del proveedor como parámetro y utiliza el servicio ProveedorService para obtener los datos del proveedor correspondiente. Si el proveedor no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el proveedor se encuentra, se devuelve un recurso ProveedorResource con los datos del proveedor. Al llamar a este método con un ID de proveedor válido, se obtiene una respuesta JSON con los detalles del proveedor solicitado.
    public function show(string $id)
    {
        $proveedor =
            $this->service->getById($id);

        if (!$proveedor) {

            return response()->json([
                'message' =>
                    'Proveedor no encontrado'
            ], 404);
        }

        return new ProveedorResource(
            $proveedor
        );
    }

    // Método para actualizar los datos de un proveedor existente. Este método recibe una solicitud de tipo UpdateProveedorRequest, que valida los datos enviados en la solicitud para actualizar un proveedor. Además, recibe el ID del proveedor a actualizar como parámetro. El método utiliza el servicio ProveedorService para obtener los datos del proveedor correspondiente. Si el proveedor no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el proveedor se encuentra, se utiliza el servicio para actualizar los datos del proveedor con los datos validados y luego se devuelve un recurso ProveedorResource con los datos del proveedor actualizado. Al llamar a este método con un ID de proveedor válido y datos de actualización válidos, se obtiene una respuesta JSON con los detalles del proveedor actualizado en la aplicación.
    public function update(
        UpdateProveedorRequest $request,
        string $id
    ) {
        $proveedor =
            $this->service->getById($id);

        if (!$proveedor) {

            return response()->json([
                'message' =>
                    'Proveedor no encontrado'
            ], 404);
        }

        $proveedorActualizado =
            $this->service->update(
                $proveedor,
                $request->validated()
            );

        return new ProveedorResource(
            $proveedorActualizado
        );
    }

    // Método para eliminar un proveedor existente. Este método recibe el ID del proveedor a eliminar como parámetro. El método utiliza el servicio ProveedorService para obtener los datos del proveedor correspondiente. Si el proveedor no se encuentra, se devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el proveedor se encuentra, se utiliza el servicio para eliminar el proveedor y luego se devuelve una respuesta JSON con un mensaje de éxito indicando que el proveedor ha sido eliminado correctamente. Al llamar a este método con un ID de proveedor válido, se obtiene una respuesta JSON confirmando la eliminación del proveedor en la aplicación.
    public function destroy(string $id)
    {
        $proveedor =
            $this->service->getById($id);

        if (!$proveedor) {

            return response()->json([
                'message' =>
                    'Proveedor no encontrado'
            ], 404);
        }

        $this->service->delete(
            $proveedor
        );

        return response()->json([
            'message' =>
                'Proveedor eliminado correctamente'
        ]);
    }
}
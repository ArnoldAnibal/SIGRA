<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmpleadoRequest;
use App\Http\Requests\UpdateEmpleadoRequest;
use App\Http\Resources\EmpleadoResource;
use App\Services\EmpleadoService;
use Illuminate\Support\Facades\Hash;

// Controlador para manejar las solicitudes relacionadas con los empleados en la API. Este controlador utiliza el EmpleadoService para realizar las operaciones de negocio relacionadas con los empleados, como obtener todos los empleados, obtener un empleado por su ID, crear un nuevo empleado, actualizar un empleado existente y eliminar un empleado. El controlador también utiliza EmpleadoResource para transformar los datos de los empleados antes de devolverlos en las respuestas de la API. Además, el controlador utiliza StoreEmpleadoRequest y UpdateEmpleadoRequest para validar los datos enviados al crear o actualizar un empleado, respectivamente.
class EmpleadoController extends Controller
{
    protected $service;

    // Constructor que recibe una instancia de EmpleadoService y la asigna a la propiedad $service. Esto permite que el controlador utilice el servicio para realizar las operaciones relacionadas con los empleados.
    public function __construct(EmpleadoService $service)
    {
        $this->service = $service;
    }

    // Método para obtener todos los empleados. Utiliza el método getAll del servicio para obtener los datos de los empleados, los transforma utilizando EmpleadoResource y devuelve una respuesta JSON con los datos de los empleados.
    public function index()
    {
        return response()->json([
            'data' => $this->service->getAll()
        ]);
    }

    // Método para crear un nuevo empleado. Utiliza el método create del servicio para crear el empleado, lo transforma utilizando EmpleadoResource y devuelve una respuesta JSON con los datos del empleado creado. Antes de crear el empleado, se valida la contraseña utilizando StoreEmpleadoRequest y se encripta utilizando Hash::make para asegurar que la contraseña se almacene de forma segura en la base de datos.
    public function store(StoreEmpleadoRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        $empleado = $this->service->create($data);

        return new EmpleadoResource($empleado);
    }

    // Método para obtener un empleado por su ID. Utiliza el método getById del servicio para obtener el empleado correspondiente al ID proporcionado, lo transforma utilizando EmpleadoResource y devuelve una respuesta JSON con los datos del empleado. Si el empleado no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
    public function show($id)
    {
        $empleado = $this->service->getById($id);

        if (!$empleado) {
            return response()->json([
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        return new EmpleadoResource($empleado);
    }

    // Método para actualizar un empleado existente. Utiliza el método getById del servicio para obtener el empleado correspondiente al ID proporcionado, lo transforma utilizando EmpleadoResource y devuelve una respuesta JSON con los datos del empleado actualizado. Si el empleado no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Antes de actualizar el empleado, se valida la contraseña utilizando UpdateEmpleadoRequest y, si se proporciona una nueva contraseña, se encripta utilizando Hash::make para asegurar que la contraseña se almacene de forma segura en la base de datos.
    public function update(UpdateEmpleadoRequest $request, $id)
    {
        $empleado = $this->service->getById($id);

        if (!$empleado) {
            return response()->json([
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $empleadoActualizado = $this->service->update(
            $empleado,
            $data
        );

        return new EmpleadoResource($empleadoActualizado);
    }

    // Método para eliminar un empleado. Utiliza el método getById del servicio para obtener el empleado correspondiente al ID proporcionado, lo transforma utilizando EmpleadoResource y devuelve una respuesta JSON con los datos del empleado eliminado. Si el empleado no se encuentra, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si el empleado se elimina correctamente, devuelve una respuesta JSON con un mensaje de éxito.
    public function destroy($id)
    {
        $empleado = $this->service->getById($id);

        if (!$empleado) {
            return response()->json([
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $this->service->delete($empleado);

        return response()->json([
            'message' => 'Empleado eliminado correctamente'
        ]);
    }
}
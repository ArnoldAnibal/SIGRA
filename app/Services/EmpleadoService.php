<?php

namespace App\Services;

use App\Repositories\EmpleadoRepository;
use App\Http\Resources\EmpleadoResource;

// Servicio para manejar la lógica de negocio relacionada con los empleados. Este servicio utiliza el EmpleadoRepository para interactuar con la base de datos y proporciona métodos para obtener todos los empleados, obtener un empleado por su ID, crear un nuevo empleado, actualizar un empleado existente y eliminar un empleado. El servicio también utiliza EmpleadoResource para transformar los datos de los empleados antes de devolverlos a las capas superiores de la aplicación.
class EmpleadoService
{
    protected $repository;

    // Constructor que recibe una instancia de EmpleadoRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con los empleados.
    public function __construct(EmpleadoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para obtener todos los empleados. Utiliza el método getAll del repositorio para obtener los datos de los empleados y luego los transforma utilizando EmpleadoResource antes de devolverlos.
    public function getAll()
    {
        return EmpleadoResource::collection(
            $this->repository->getAll()
        );
    }

    // Método para obtener un empleado por su ID. Utiliza el método findById del repositorio para obtener el empleado correspondiente al ID proporcionado y luego lo transforma utilizando EmpleadoResource antes de devolverlo.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo empleado. Recibe un array de datos, utiliza el método create del repositorio para crear el empleado en la base de datos y devuelve el empleado creado.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Método para actualizar un empleado existente. Recibe el empleado a actualizar y un array de datos con los nuevos valores, utiliza el método update del repositorio para actualizar el empleado en la base de datos y devuelve el empleado actualizado.
    public function update($empleado, array $data)
    {
        return $this->repository->update($empleado, $data);
    }

    // Método para eliminar un empleado. Recibe el empleado a eliminar, utiliza el método delete del repositorio para eliminar el empleado de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($empleado)
    {
        return $this->repository->delete($empleado);
    }
}
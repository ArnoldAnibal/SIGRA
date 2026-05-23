<?php

namespace App\Services;

use App\Repositories\ClienteRepository;
use App\Http\Resources\ClienteResource;


// Servicio para manejar la lógica de negocio relacionada con los clientes. Este servicio utiliza el ClienteRepository para interactuar con la base de datos y proporciona métodos para obtener todos los clientes, obtener un cliente por su ID, crear un nuevo cliente, actualizar un cliente existente y eliminar un cliente. El servicio también utiliza ClienteResource para transformar los datos de los clientes antes de devolverlos a las capas superiores de la aplicación.
class ClienteService
{

    // Propiedad para almacenar la instancia del repositorio de clientes.
    protected $repository;

    // Constructor que recibe una instancia de ClienteRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con los clientes.
    public function __construct(ClienteRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para obtener todos los clientes. Utiliza el método getAll del repositorio para obtener los datos de los clientes y luego los transforma utilizando ClienteResource antes de devolverlos.
    public function getAll()
    {
        return ClienteResource::collection(
            $this->repository->getAll()
        );
    }

    // Método para obtener un cliente por su ID. Utiliza el método findById del repositorio para obtener el cliente correspondiente al ID proporcionado y luego lo transforma utilizando ClienteResource antes de devolverlo.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo cliente. Recibe un array de datos, utiliza el método create del repositorio para crear el cliente en la base de datos y devuelve el cliente creado.
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    // Método para actualizar un cliente existente. Recibe el cliente a actualizar y un array de datos con los nuevos valores, utiliza el método update del repositorio para actualizar el cliente en la base de datos y devuelve el cliente actualizado.
    public function update($cliente, array $data)
    {
        return $this->repository->update($cliente, $data);
    }

    // Método para eliminar un cliente. Recibe el cliente a eliminar, utiliza el método delete del repositorio para eliminar el cliente de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($cliente)
    {
        return $this->repository->delete($cliente);
    }
}
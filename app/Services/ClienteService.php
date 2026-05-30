<?php

namespace App\Services;

use App\Repositories\ClienteRepository;
use Illuminate\Support\Facades\Hash;

// Servicio para gestionar las operaciones relacionadas con los clientes, utilizando el repositorio ClienteRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todos los clientes, encontrar un cliente por su ID, crear un nuevo cliente, actualizar un cliente existente y eliminar un cliente.
class ClienteService
{
    protected $clienteRepository;

    // Constructor que recibe una instancia de ClienteRepository y la asigna a la propiedad $clienteRepository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con los clientes.
    public function __construct(ClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }

    // Método para obtener todos los clientes. Utiliza el método getAll del repositorio para obtener los datos de los clientes y luego los devuelve.
    public function getAll()
    {
        return $this->clienteRepository->getAll();
    }

    // Método para obtener un cliente por su ID. Utiliza el método findById del repositorio para obtener el cliente correspondiente al ID proporcionado y luego lo devuelve.
    public function findById(int $id)
    {
        return $this->clienteRepository->findById($id);
    }

    // Método para crear un nuevo cliente. Recibe un array de datos, hashea la contraseña si está presente, establece valores predeterminados para el saldo de crédito actual y el límite de crédito si no se proporcionan, utiliza el método create del repositorio para crear el cliente en la base de datos y devuelve el cliente creado.
    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        $data['saldo_credito_actual'] = $data['saldo_credito_actual'] ?? 0;
        $data['limite_credito'] = $data['limite_credito'] ?? 0;

        return $this->clienteRepository->create($data);
    }

    // Método para actualizar un cliente existente. Recibe el ID del cliente a actualizar y un array de datos con los nuevos valores. Si el cliente no existe, devuelve null. Si se proporciona una nueva contraseña, se hashea antes de actualizar el cliente. Luego, utiliza el método update del repositorio para actualizar el cliente en la base de datos y devuelve el cliente actualizado.
    public function update(int $id, array $data)
    {
        $cliente = $this->clienteRepository->findById($id);

        if (!$cliente) {
            return null;
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->clienteRepository->update($cliente, $data);
    }

    // Método para eliminar un cliente. Recibe el ID del cliente a eliminar, verifica si el cliente existe y luego utiliza el método delete del repositorio para eliminar el cliente de la base de datos. Devuelve un booleano indicando si la operación fue exitosa.
    public function delete(int $id)
    {
        $cliente = $this->clienteRepository->findById($id);

        if (!$cliente) {
            return false;
        }

        return $this->clienteRepository->delete($cliente);
    }
}
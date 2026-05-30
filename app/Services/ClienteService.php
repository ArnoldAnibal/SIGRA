<?php

namespace App\Services;

use App\Repositories\ClienteRepository;
use Illuminate\Support\Facades\Hash;

// Servicio para gestionar las operaciones relacionadas con los clientes, utilizando el repositorio ClienteRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todos los clientes, encontrar un cliente por su ID, crear un nuevo cliente, actualizar un cliente existente y eliminar un cliente.
class ClienteService
{
    protected $clienteRepository;

    public function __construct(ClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }

    public function getAll()
    {
        return $this->clienteRepository->getAll();
    }

    public function findById(int $id)
    {
        return $this->clienteRepository->findById($id);
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        $data['saldo_credito_actual'] = $data['saldo_credito_actual'] ?? 0;
        $data['limite_credito'] = $data['limite_credito'] ?? 0;

        return $this->clienteRepository->create($data);
    }

    /**
     * FIXED:
     * Now ONLY accepts ID, NOT model
     */
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

    /**
     * FIXED:
     * Now ONLY accepts ID, NOT model
     */
    public function delete(int $id)
    {
        $cliente = $this->clienteRepository->findById($id);

        if (!$cliente) {
            return false;
        }

        return $this->clienteRepository->delete($cliente);
    }
}
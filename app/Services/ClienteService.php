<?php

namespace App\Services;

use App\Repositories\ClienteRepository;
use Illuminate\Support\Facades\Hash;

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

    public function findById($id)
    {
        return $this->clienteRepository->findById($id);
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        if (!isset($data['saldo_credito_actual'])) {
            $data['saldo_credito_actual'] = 0;
        }

        if (!isset($data['limite_credito'])) {
            $data['limite_credito'] = 0;
        }

        return $this->clienteRepository->create($data);
    }

    public function update($id, array $data)
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

    public function delete($id)
    {
        $cliente = $this->clienteRepository->findById($id);

        if (!$cliente) {
            return false;
        }

        return $this->clienteRepository->delete($cliente);
    }
}
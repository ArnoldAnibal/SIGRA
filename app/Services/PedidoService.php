<?php

namespace App\Services;

use App\Repositories\PedidoRepository;
use App\Models\Cliente;
use Exception;

// Servicio para manejar la lógica de negocio relacionada con los pedidos.
// Ahora incluye validación de crédito para clientes empresariales.
class PedidoService
{
    protected $repository;

    // Constructor que recibe una instancia de PedidoRepository.
    public function __construct(PedidoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Obtener todos los pedidos
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Obtener pedido por ID
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Crear un nuevo pedido
    public function create(array $data)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN DE CRÉDITO EMPRESARIAL
        |--------------------------------------------------------------------------
        |
        | Si el pedido será pagado con crédito:
        | - El cliente debe existir
        | - Debe ser tipo Empresa
        | - Debe estar Activo
        | - Debe tener suficiente crédito disponible
        |
        */

        if (
            isset($data['metodo_pago']) &&
            $data['metodo_pago'] === 'Credito'
        ) {

            $cliente = Cliente::find($data['id_cliente']);

            // Validar existencia del cliente
            if (!$cliente) {
                throw new Exception('Cliente no encontrado.');
            }

            // Validar que sea empresa
            if ($cliente->tipo_cliente !== 'Empresa') {
                throw new Exception(
                    'Solo los clientes empresariales pueden usar crédito.'
                );
            }

            // Validar estado del cliente
            if ($cliente->estado !== 'Activo') {
                throw new Exception(
                    'El cliente empresarial está suspendido.'
                );
            }

            // Calcular nuevo saldo
            $nuevoSaldo =
                $cliente->saldo_credito_actual + $data['total'];

            // Validar límite de crédito
            if ($nuevoSaldo > $cliente->limite_credito) {

                $creditoDisponible =
                    $cliente->limite_credito -
                    $cliente->saldo_credito_actual;

                throw new Exception(
                    'Crédito insuficiente. Crédito disponible: Q' .
                    number_format($creditoDisponible, 2)
                );
            }

            // Actualizar saldo de crédito
            $cliente->saldo_credito_actual = $nuevoSaldo;
            $cliente->save();
        }

        // Crear pedido
        return $this->repository->create($data);
    }

    // Actualizar pedido existente
    public function update($pedido, array $data)
    {
        return $this->repository->update($pedido, $data);
    }

    // Eliminar pedido
    public function delete($pedido)
    {
        return $this->repository->delete($pedido);
    }
}
<?php

namespace App\Services;

use App\Repositories\FacturaElectronicaRepository;
use App\Models\Cliente;

class FacturaElectronicaService
{
    protected $repository;

    public function __construct(
        FacturaElectronicaRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {
        /*
        |--------------------------------------------------------------------------
        | FACTURAS A CRÉDITO
        |--------------------------------------------------------------------------
        |
        | El crédito ya fue validado y descontado
        | al momento de crear el pedido.
        |
        | Aquí solamente:
        | - Validamos datos
        | - Marcamos estado_pago
        |
        */

        if (
            isset($data['metodo_pago']) &&
            $data['metodo_pago'] === 'Credito'
        ) {

            if (!isset($data['id_cliente_deudor'])) {
                throw new \Exception(
                    'La factura a crédito requiere cliente deudor.'
                );
            }

            if (!isset($data['fecha_vencimiento'])) {
                throw new \Exception(
                    'La factura a crédito requiere fecha de vencimiento.'
                );
            }

            $cliente = Cliente::find(
                $data['id_cliente_deudor']
            );

            if (!$cliente) {
                throw new \Exception(
                    'Cliente deudor no encontrado.'
                );
            }

            if ($cliente->tipo_cliente !== 'Empresa') {
                throw new \Exception(
                    'Solo clientes empresa pueden usar crédito.'
                );
            }

            $data['estado_pago'] = 'Pendiente';

        } else {

            $data['estado_pago'] = 'Pagada';
        }

        return $this->repository->create($data);
    }

public function update($factura, array $data)
{
    // If invoice is being marked as paid
    if (
        isset($data['estado_pago']) &&
        $data['estado_pago'] === 'Pagada' &&
        $factura->estado_pago !== 'Pagada'
    ) {
        // set payment timestamp
        $data['fecha_pago'] = now()->format('Y-m-d H:i:s');

        // release credit (IMPORTANT)
        $cliente = Cliente::find($factura->id_cliente_deudor);

        if ($cliente) {
            $cliente->saldo_credito_actual =
                max(0, $cliente->saldo_credito_actual - $factura->monto_total);

            $cliente->save();
        }
    }

    // If invoice is being cancelled
    if (
        isset($data['estado']) &&
        $data['estado'] === 'Anulada' &&
        $factura->estado === 'Emitida'
    ) {
        $cliente = Cliente::find($factura->id_cliente_deudor);

        if ($cliente) {
            $cliente->saldo_credito_actual =
                max(0, $cliente->saldo_credito_actual - $factura->monto_total);

            $cliente->save();
        }
    }

    return $this->repository->update($factura, $data);
}

    public function delete($factura)
    {
        return $this->repository->delete(
            $factura
        );
    }
}
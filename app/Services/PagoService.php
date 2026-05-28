<?php

namespace App\Services;

use App\Repositories\PagoRepository;
use App\Models\FacturaElectronica;
use App\Models\Cliente;
use Exception;

class PagoService
{
    protected $repository;

    public function __construct(PagoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Obtener todos los pagos
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Obtener pago por ID
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Crear pago
    public function create(array $data)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDAR FACTURA
        |--------------------------------------------------------------------------
        */

        $factura = FacturaElectronica::find(
            $data['id_factura']
        );

        if (!$factura) {
            throw new Exception(
                'Factura no encontrada.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR FACTURA ANULADA
        |--------------------------------------------------------------------------
        */

        if ($factura->estado === 'Anulada') {
            throw new Exception(
                'No se puede pagar una factura anulada.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR MONTO
        |--------------------------------------------------------------------------
        */

        if ($data['monto'] <= 0) {
            throw new Exception(
                'El monto debe ser mayor a cero.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR PAGO
        |--------------------------------------------------------------------------
        */

        $pago = $this->repository->create($data);

        /*
        |--------------------------------------------------------------------------
        | SI ES FACTURA A CRÉDITO
        |--------------------------------------------------------------------------
        */

        if (
            $factura->metodo_pago === 'Credito' &&
            $factura->id_cliente_deudor
        ) {

            $cliente = Cliente::find(
                $factura->id_cliente_deudor
            );

            if ($cliente) {

                /*
                |--------------------------------------------------------------------------
                | REDUCIR SALDO DE CRÉDITO
                |--------------------------------------------------------------------------
                */

                $cliente->saldo_credito_actual -=
                    $data['monto'];

                // Evitar negativos
                if ($cliente->saldo_credito_actual < 0) {
                    $cliente->saldo_credito_actual = 0;
                }

                $cliente->save();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CALCULAR TOTAL PAGADO
        |--------------------------------------------------------------------------
        */

        $totalPagado =
            $factura->pagos()->sum('monto');

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR ESTADO FACTURA
        |--------------------------------------------------------------------------
        */

        if ($totalPagado >= $factura->monto_total) {

            $factura->estado_pago = 'Pagada';

        } else {

            $factura->estado_pago = 'Pendiente';
        }

        $factura->save();

        return $pago;
    }

    // Actualizar pago
    public function update($pago, array $data)
    {
        return $this->repository->update(
            $pago,
            $data
        );
    }

    // Eliminar pago
    public function delete($pago)
    {
        return $this->repository->delete(
            $pago
        );
    }
}
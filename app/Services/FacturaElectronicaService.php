<?php

namespace App\Services;

use App\Repositories\FacturaElectronicaRepository;
use App\Models\Cliente;

// Servicio para gestionar las facturas electrónicas, utilizando el repositorio FacturaElectronicaRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todas las facturas, encontrar una factura por su ID, crear una nueva factura, actualizar una factura existente y eliminar una factura.
class FacturaElectronicaService
{
    protected $repository;

    // Constructor que recibe una instancia de FacturaElectronicaRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con las facturas electrónicas.
    public function __construct(
        FacturaElectronicaRepository $repository
    ) {
        $this->repository = $repository;
    }

    // Método para obtener todas las facturas electrónicas. Utiliza el método getAll del repositorio para obtener los datos de las facturas y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener una factura electrónica por su ID. Utiliza el método findById del repositorio para obtener la factura correspondiente al ID proporcionado y luego la devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear una nueva factura electrónica. Recibe un array de datos, valida la información relacionada con el crédito si es necesario, establece el estado de pago y luego utiliza el método create del repositorio para crear la factura en la base de datos y devuelve la factura creada. Si la factura es a crédito, también se verifica que el cliente deudor exista y sea del tipo "Empresa". Si la factura se marca como pagada o anulada, se actualiza el saldo de crédito del cliente deudor en consecuencia. 
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

    // Método para actualizar una factura electrónica existente. Recibe la factura a actualizar y un array de datos con los nuevos valores. Si la factura se marca como pagada o anulada, se actualiza el saldo de crédito del cliente deudor en consecuencia. Luego, utiliza el método update del repositorio para actualizar la factura en la base de datos y devuelve la factura actualizada.
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

// Método para eliminar una factura electrónica. Recibe la factura a eliminar y lo pasa al método delete del repositorio. Devuelve un booleano indicando si la operación fue exitosa.
    public function delete($factura)
    {
        return $this->repository->delete(
            $factura
        );
    }
}
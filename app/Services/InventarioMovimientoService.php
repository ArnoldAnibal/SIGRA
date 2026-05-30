<?php

namespace App\Services;

use App\Models\ProductoMenu;
use App\Repositories\InventarioMovimientoRepository;
use Exception;



// Servicio para gestionar los movimientos de inventario, utilizando el repositorio InventarioMovimientoRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todos los movimientos, encontrar un movimiento por su ID, crear un nuevo movimiento, actualizar un movimiento existente y eliminar un movimiento. Además, al crear un nuevo movimiento, se actualiza el stock del producto correspondiente según el tipo de movimiento (Entrada o Salida) y se verifica que el stock sea suficiente en caso de una salida.
class InventarioMovimientoService
{
    protected $repository;

    public function __construct(
        InventarioMovimientoRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {
        $producto = ProductoMenu::find(
            $data['id_producto']
        );

        if (!$producto) {
            throw new Exception(
                'Producto no encontrado'
            );
        }

        if ($data['tipo'] === 'Entrada') {

            $producto->stock +=
                $data['cantidad'];

        } else {

            if (
                $producto->stock <
                $data['cantidad']
            ) {

                throw new Exception(
                    'Stock insuficiente'
                );
            }

            $producto->stock -=
                $data['cantidad'];
        }

        $producto->save();

        return $this->repository->create(
            $data
        );
    }

    public function update(
        $movimiento,
        array $data
    ) {
        return $this->repository->update(
            $movimiento,
            $data
        );
    }

    public function delete(
        $movimiento
    ) {
        return $this->repository->delete(
            $movimiento
        );
    }
}
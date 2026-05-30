<?php

namespace App\Services;

use App\Models\ProductoMenu;
use App\Repositories\InventarioMovimientoRepository;
use Exception;



// Servicio para gestionar los movimientos de inventario, utilizando el repositorio InventarioMovimientoRepository para realizar las operaciones de acceso a datos. Este servicio proporciona métodos para obtener todos los movimientos, encontrar un movimiento por su ID, crear un nuevo movimiento, actualizar un movimiento existente y eliminar un movimiento. Además, al crear un nuevo movimiento, se actualiza el stock del producto correspondiente según el tipo de movimiento (Entrada o Salida) y se verifica que el stock sea suficiente en caso de una salida.
class InventarioMovimientoService
{
    protected $repository;

    // Constructor que recibe una instancia de InventarioMovimientoRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar operaciones relacionadas con los movimientos de inventario.
    public function __construct(
        InventarioMovimientoRepository $repository
    ) {
        $this->repository = $repository;
    }

    // Método para obtener todos los movimientos de inventario. Utiliza el método getAll del repositorio para obtener los datos de los movimientos y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener un movimiento de inventario por su ID. Utiliza el método findById del repositorio para obtener el movimiento correspondiente al ID proporcionado y luego lo devuelve.
    public function getById($id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo movimiento de inventario. Recibe un array de datos, verifica que el producto exista y actualiza el stock del producto según el tipo de movimiento (Entrada o Salida). Si el tipo es "Salida", también verifica que el stock sea suficiente antes de realizar la operación. Finalmente, utiliza el método create del repositorio para crear el movimiento en la base de datos y devuelve el movimiento creado.
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

    // Método para actualizar un movimiento de inventario existente. Recibe el movimiento a actualizar y un array de datos con los nuevos valores. Devuelve el movimiento actualizado.
    public function update(
        $movimiento,
        array $data
    ) {
        return $this->repository->update(
            $movimiento,
            $data
        );
    }

    // Método para eliminar un movimiento de inventario. Recibe el movimiento a eliminar y lo pasa al método delete del repositorio.
    public function delete(
        $movimiento
    ) {
        return $this->repository->delete(
            $movimiento
        );
    }
}
<?php

namespace App\Services;
use App\Models\ProductoMenu;
use App\Models\InventarioMovimiento;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Repositories\DetallePedidoRepository;

// Servicio para manejar la lógica de negocio relacionada con el modelo DetallePedido. Este servicio utiliza el repositorio DetallePedidoRepository para realizar las operaciones de acceso a datos. El servicio proporciona métodos para obtener todos los detalles de pedido, obtener un detalle de pedido por su ID, crear un nuevo detalle de pedido, actualizar un detalle de pedido existente y eliminar un detalle de pedido. Al utilizar un servicio, se separa la lógica de negocio de la lógica de acceso a datos, lo que mejora la organización del código y facilita su mantenimiento.
class DetallePedidoService
{
    protected $repository;

    // Constructor que recibe una instancia de DetallePedidoRepository y la asigna a la propiedad $repository. Esto permite que el servicio utilice el repositorio para realizar las operaciones relacionadas con los detalles de pedido.
    public function __construct(DetallePedidoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para obtener todos los detalles de pedido. Utiliza el método getAll del repositorio para obtener los datos de los detalles de pedido y luego los devuelve.
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // Método para obtener un detalle de pedido por su ID. Utiliza el método findById del repositorio para obtener el detalle de pedido correspondiente al ID proporcionado y luego lo devuelve.
    public function getById(int $id)
    {
        return $this->repository->findById($id);
    }

    // Método para crear un nuevo detalle de pedido. Recibe un array de datos, verifica que el producto exista y actualiza el stock del producto según el tipo de movimiento (Entrada o Salida). Si el tipo es "Salida", también verifica que el stock sea suficiente antes de realizar la operación. Finalmente, utiliza el método create del repositorio para crear el detalle de pedido en la base de datos y devuelve el detalle de pedido creado. Además, se actualiza el total del pedido asociado al detalle de pedido después de crear el nuevo detalle de pedido. Toda esta operación se realiza dentro de una transacción de base de datos para garantizar la integridad de los datos en caso de que ocurra algún error durante el proceso.
    public function create(array $data)
{
    return DB::transaction(function () use ($data) {

        $producto = ProductoMenu::find(
            $data['id_producto']
        );

        if (!$producto) {
            throw new Exception(
                'Producto no encontrado'
            );
        }

        if (
            $producto->stock <
            $data['cantidad']
        ) {
            throw new Exception(
                'Stock insuficiente para el producto: '
                . $producto->nombre
            );
        }

        $data['subtotal'] =
            $producto->precio_venta *
            $data['cantidad'];

        $detalle =
            $this->repository->create($data);

        $producto->stock =
            $producto->stock -
            $data['cantidad'];

        $producto->save();

        InventarioMovimiento::create([
            'id_producto' => $producto->id_producto,
            'tipo' => 'Salida',
            'cantidad' => $data['cantidad'],
            'motivo' =>
                'Pedido #' .
                $data['id_pedido']
        ]);

        $pedido = Pedido::find(
            $data['id_pedido']
        );

        if ($pedido) {

            $pedido->total =
                $pedido->detalles()
                    ->sum('subtotal');

            $pedido->save();
        }

        return $detalle;
    });
}
    // Método para actualizar un detalle de pedido existente. Recibe el detalle de pedido a actualizar y un array de datos con los nuevos valores, utiliza el método update del repositorio para actualizar el detalle de pedido en la base de datos y devuelve el detalle de pedido actualizado.
    public function update($detalle, array $data)
    {
        return $this->repository->update($detalle, $data);
    }

    // Método para eliminar un detalle de pedido. Recibe el detalle de pedido a eliminar, utiliza el método delete del repositorio para eliminar el detalle de pedido de la base de datos y devuelve un booleano indicando si la operación fue exitosa.
    public function delete($detalle)
    {
        return $this->repository->delete($detalle);
    }
}
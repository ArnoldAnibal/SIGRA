<?php

namespace App\Http\Controllers\Api;

// Controlador para manejar las solicitudes relacionadas con la entidad ProductoMenu, proporcionando métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) en los productos del menú. Este controlador utiliza el servicio ProductoMenuService para interactuar con la lógica de negocio y la base de datos, y devuelve respuestas JSON adecuadas para cada operación, incluyendo validaciones de entrada y manejo de errores cuando un producto no es encontrado.
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoMenuRequest;
use App\Http\Requests\UpdateProductoMenuRequest;
use App\Http\Resources\ProductoMenuResource;
use Illuminate\Http\Request;
use App\Services\ProductoMenuService;

class ProductoMenuController extends Controller
{
    // El constructor recibe una instancia del servicio ProductoMenuService a través de inyección de dependencias, lo que permite al controlador utilizar el servicio para realizar operaciones CRUD en la base de datos y aplicar cualquier lógica de negocio necesaria antes de devolver las respuestas al cliente.
    protected $service;

    // El constructor recibe una instancia del servicio ProductoMenuService a través de inyección de dependencias, lo que permite al controlador utilizar el servicio para realizar operaciones CRUD en la base de datos y aplicar cualquier lógica de negocio necesaria antes de devolver las respuestas al cliente.
    public function __construct(ProductoMenuService $service)
    {
        $this->service = $service;
    }

    // Obtener todos los productos del menú, devolviendo una respuesta JSON con una colección de productos utilizando el método getAll() del servicio y formateando la respuesta con el recurso ProductoMenuResource.
    public function index()
    {
        return response()->json([
            // 'message' => 'Lista de productos del menú',
            'data' => $this->service->getAll()
        ]);
    }

    // Crear producto con validación de datos, asegurando que los campos requeridos estén presentes y sean válidos. Devuelve el producto creado formateado con el recurso ProductoMenuResource y un código de estado 201 si la creación es exitosa. Recibe el Form Request StoreProductoMenuRequest para validar los datos de entrada.
    public function store(StoreProductoMenuRequest $request)
    {
        // Crear un nuevo producto utilizando el método create() del servicio, pasando los datos validados desde el Form Request StoreProductoMenuRequest. Devuelve el producto creado formateado con el recurso ProductoMenuResource.
        $producto = $this->service->create($request->validated());

        return new ProductoMenuResource($producto);
    }

    // Obtener producto por ID y devolverlo formateado con el recurso ProductoMenuResource. Si el producto no existe, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Recibe el ID del producto a obtener como parámetro.
    public function show($id)
    {
        $producto = $this->service->getById($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return new ProductoMenuResource($producto);
    }

    // Actualizar producto con validación de datos, asegurando que los campos sean válidos y que el producto exista antes de intentar actualizarlo. Devuelve el producto actualizado formateado con el recurso ProductoMenuResource. Si el producto no existe, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Recibe el Form Request UpdateProductoMenuRequest para validar los datos de entrada y el ID del producto a actualizar como parámetro.
    public function update(UpdateProductoMenuRequest $request, $id)
    {
        $producto = $this->service->getById($id);

        if (!$producto) {
            return response()->json([
                // Si el producto no existe, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Actualiza el producto utilizando el método update() del servicio, pasando el producto existente y los datos validados desde el Form Request UpdateProductoMenuRequest. Devuelve el producto actualizado formateado con el recurso ProductoMenuResource.
        $productoActualizado = $this->service->update(
            $producto,
            $request->validated()
        );

        return new ProductoMenuResource($productoActualizado);
    }

    // Eliminar producto de forma permanente de la base de datos. Devuelve una respuesta JSON con un mensaje indicando que el producto fue eliminado correctamente. Si el producto no existe, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Recibe el ID del producto a eliminar como parámetro.
    public function destroy($id)
    {
        $producto = $this->service->getById($id);

        if (!$producto) {
            // Si el producto no existe, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $this->service->delete($producto);

        // Devuelve una respuesta JSON con un mensaje indicando que el producto fue eliminado correctamente.
        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ]);
    }
}
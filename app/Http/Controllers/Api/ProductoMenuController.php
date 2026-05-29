<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoMenuRequest;
use App\Http\Requests\UpdateProductoMenuRequest;
use App\Http\Resources\ProductoMenuResource;
use App\Services\ProductoMenuService;

class ProductoMenuController extends Controller
{
    protected $service;

    public function __construct(ProductoMenuService $service)
    {
        $this->service = $service;
    }

    /**
     * Obtener todos los productos
     */
    public function index()
    {
        return response()->json([
            'data' => $this->service->getAll()
        ]);
    }

    /**
     * Crear producto
     */
    public function store(StoreProductoMenuRequest $request)
{
    $data = $request->validated();

    // Imagen opcional
    if ($request->hasFile('imagen')) {

        $path = $request
            ->file('imagen')
            ->store('productos', 'public');

        $data['imagen'] = $path;
    }

    $producto = $this->service->create($data);

    return new ProductoMenuResource($producto);
}

    /**
     * Mostrar producto por ID
     */
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

    /**
     * Actualizar producto
     */
    public function update(UpdateProductoMenuRequest $request, $id)
{
    $producto = $this->service->getById($id);

    if (!$producto) {
        return response()->json([
            'message' => 'Producto no encontrado'
        ], 404);
    }

    $data = $request->validated();

    if ($request->hasFile('imagen')) {
        $path = $request->file('imagen')->store('productos', 'public');
        $data['imagen'] = $path;
    }

    
    $productoActualizado = $this->service->update($producto, $data);

    return new ProductoMenuResource($productoActualizado);
}

    /**
     * Eliminar producto
     */
    public function destroy($id)
    {
        $producto = $this->service->getById($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $this->service->delete($producto);

        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ]);
    }
}
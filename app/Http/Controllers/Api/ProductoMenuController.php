<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoMenuRequest;
use App\Http\Requests\UpdateProductoMenuRequest;
use App\Http\Resources\ProductoMenuResource;
use Illuminate\Http\Request;
use App\Services\ProductoMenuService;

class ProductoMenuController extends Controller
{
    protected $service;

    public function __construct(ProductoMenuService $service)
    {
        $this->service = $service;
    }

    // Obtener todos los productos
    public function index()
    {
        return response()->json([
            'data' => $this->service->getAll()
        ]);
    }

    // Crear producto
    public function store(StoreProductoMenuRequest $request)
    {
        $producto = $this->service->create($request->validated());

        return new ProductoMenuResource($producto);
    }

    // Obtener producto por ID
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

    // Actualizar producto
    public function update(UpdateProductoMenuRequest $request, $id)
    {
        $producto = $this->service->getById($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $productoActualizado = $this->service->update(
            $producto,
            $request->validated()
        );

        return new ProductoMenuResource($productoActualizado);
    }

    // Eliminar producto
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
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Http\Resources\ProveedorResource;
use App\Services\ProveedorService;

class ProveedorController extends Controller
{
    protected $service;

    public function __construct(
        ProveedorService $service
    ) {
        $this->service = $service;
    }

    public function index()
    {
        return ProveedorResource::collection(
            $this->service->getAll()
        );
    }

    public function store(
        StoreProveedorRequest $request
    ) {
        $proveedor = $this->service->create(
            $request->validated()
        );

        return new ProveedorResource(
            $proveedor
        );
    }

    public function show(string $id)
    {
        $proveedor =
            $this->service->getById($id);

        if (!$proveedor) {

            return response()->json([
                'message' =>
                    'Proveedor no encontrado'
            ], 404);
        }

        return new ProveedorResource(
            $proveedor
        );
    }

    public function update(
        UpdateProveedorRequest $request,
        string $id
    ) {
        $proveedor =
            $this->service->getById($id);

        if (!$proveedor) {

            return response()->json([
                'message' =>
                    'Proveedor no encontrado'
            ], 404);
        }

        $proveedorActualizado =
            $this->service->update(
                $proveedor,
                $request->validated()
            );

        return new ProveedorResource(
            $proveedorActualizado
        );
    }

    public function destroy(string $id)
    {
        $proveedor =
            $this->service->getById($id);

        if (!$proveedor) {

            return response()->json([
                'message' =>
                    'Proveedor no encontrado'
            ], 404);
        }

        $this->service->delete(
            $proveedor
        );

        return response()->json([
            'message' =>
                'Proveedor eliminado correctamente'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaMenuRequest;
use App\Http\Requests\UpdateCategoriaMenuRequest;
use App\Http\Resources\CategoriaMenuResource;
use App\Models\CategoriaMenu;
use App\Services\CategoriaMenuService;

class CategoriaMenuController extends Controller
{
    protected $service;

    public function __construct(CategoriaMenuService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return CategoriaMenuResource::collection(
            $this->service->getAll()
        );
    }

    public function show($id)
    {
        $categoria = $this->service->getById($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        return new CategoriaMenuResource($categoria);
    }

    public function store(StoreCategoriaMenuRequest $request)
    {
        $categoria = $this->service->create(
            $request->validated()
        );

        return new CategoriaMenuResource($categoria);
    }

    public function update(UpdateCategoriaMenuRequest $request, $id)
    {
        $categoria = CategoriaMenu::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $categoria = $this->service->update(
            $categoria,
            $request->validated()
        );

        return new CategoriaMenuResource($categoria);
    }

    public function destroy($id)
    {
        $categoria = CategoriaMenu::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $this->service->delete($categoria);

        return response()->json([
            'message' => 'Categoría eliminada correctamente'
        ]);
    }
}
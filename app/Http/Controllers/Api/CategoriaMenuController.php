<?php

namespace App\Http\Controllers\Api;

// Controlador para manejar las solicitudes relacionadas con la entidad CategoriaMenu, utilizando el servicio CategoriaMenuService para realizar operaciones CRUD y devolver respuestas formateadas utilizando el recurso CategoriaMenuResource. Este controlador se encarga de recibir las solicitudes HTTP, validar los datos utilizando los Form Requests StoreCategoriaMenuRequest y UpdateCategoriaMenuRequest, y devolver respuestas JSON adecuadas para cada operación.
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaMenuRequest;
use App\Http\Requests\UpdateCategoriaMenuRequest;
use App\Http\Resources\CategoriaMenuResource;
use App\Models\CategoriaMenu;
use App\Services\CategoriaMenuService;

class CategoriaMenuController extends Controller
{
    protected $service;

    // El constructor recibe una instancia del servicio CategoriaMenuService a través de inyección de dependencias, lo que permite al controlador utilizar el servicio para realizar operaciones CRUD en la base de datos y aplicar cualquier lógica de negocio necesaria antes de devolver las respuestas al cliente.
    public function __construct(CategoriaMenuService $service)
    {
        $this->service = $service;
    }

    // Devuelve una colección de todas las categorías de menú disponibles en la base de datos utilizando el método getAll() del servicio y formateando la respuesta con el recurso CategoriaMenuResource.
    public function index()
    {
        return CategoriaMenuResource::collection(
            $this->service->getAll()
        );
    }

    // Busca una categoría de menú por su ID utilizando el método getById() del servicio. Si la categoría no existe, devuelve una respuesta JSON con un mensaje de error y un código de estado 404. Si la categoría existe, devuelve la categoría formateada con el recurso CategoriaMenuResource.
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

    // Crea una nueva categoría de menú utilizando el método create() del servicio. Recibe un arreglo de datos validados desde el Form Request StoreCategoriaMenuRequest y devuelve la categoría creada formateada con el recurso CategoriaMenuResource.
    public function store(StoreCategoriaMenuRequest $request)
    {
        $categoria = $this->service->create(
            $request->validated()
        );

        return new CategoriaMenuResource($categoria);
    }

    // Actualiza una categoría de menú existente con los nuevos datos proporcionados. Devuelve la categoría actualizada formateada con el recurso CategoriaMenuResource. Recibe el Form Request UpdateCategoriaMenuRequest y el ID de la categoría a actualizar.
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

    // Elimina una categoría de menú existente de la base de datos. Devuelve una respuesta JSON con un mensaje indicando que la categoría fue eliminada correctamente. Recibe el ID de la categoría a eliminar. Si la categoría no existe, devuelve una respuesta JSON con un mensaje de error y un código de estado
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
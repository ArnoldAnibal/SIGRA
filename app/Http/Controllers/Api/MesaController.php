<?php

namespace App\Http\Controllers\Api;

// Controlador para manejar las solicitudes relacionadas con la entidad Mesa, proporcionando métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) en las mesas del restaurante. Este controlador utiliza el modelo Mesa para interactuar con la base de datos y devuelve respuestas JSON adecuadas para cada operación, incluyendo validaciones de entrada y manejo de errores cuando una mesa no es encontrada.

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Http\Requests\StoreMesaRequest;
use App\Http\Requests\UpdateMesaRequest;

class MesaController extends Controller
{
    // Obtener todas las mesas
    public function index()
    {
        return response()->json(Mesa::all());
    }

    // Crear nueva mesa
    public function store(StoreMesaRequest $request)
    {
        $mesa = Mesa::create($request->validated());

        return response()->json($mesa, 201);
    }

    // Obtener una mesa específica por su ID
    public function show($id)
    {
        $mesa = Mesa::find($id);

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa no encontrada'
            ], 404);
        }

        return response()->json($mesa);
    }

    // Actualizar una mesa
    public function update(UpdateMesaRequest $request, $id)
    {
        $mesa = Mesa::find($id);

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa no encontrada'
            ], 404);
        }

        $mesa->update($request->validated());

        return response()->json($mesa);
    }

    // Soft delete de una mesa
    public function destroy($id)
    {
        $mesa = Mesa::find($id);

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa no encontrada'
            ], 404);
        }

        $mesa->delete();

        return response()->json([
            'message' => 'Mesa eliminada correctamente'
        ]);
    }
}
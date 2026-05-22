<?php

namespace App\Http\Controllers\Api;

// Controlador para manejar las solicitudes relacionadas con la entidad Mesa, proporcionando métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) en las mesas del restaurante. Este controlador utiliza el modelo Mesa para interactuar con la base de datos y devuelve respuestas JSON adecuadas para cada operación, incluyendo validaciones de entrada y manejo de errores cuando una mesa no es encontrada.
use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    // Obtener todas las mesas
    public function index()
    {
        return response()->json(Mesa::all());
    }

    // Crear nueva mesa con validación de datos, asegurando que el número de mesa sea único y que los campos requeridos estén presentes. Devuelve la mesa creada con un código de estado 201 si la creación es exitosa.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_mesa' => 'required|integer|unique:Mesa,numero_mesa',
            'capacidad' => 'required|integer',
            'estado' => 'required|in:Libre,Ocupada,Reservada,Mantenimiento'
        ]);

        $mesa = Mesa::create($validated);

        return response()->json($mesa, 201);
    }

    // Obtener una mesa específica por su ID, devolviendo una respuesta JSON con la mesa encontrada o un mensaje de error si la mesa no existe en la base de datos.
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

    // Actualizar mesa con validación de datos, asegurando que los campos sean válidos y que el número de mesa sea único si se proporciona. Devuelve la mesa actualizada con un código de estado 200 si la actualización es exitosa.
    public function update(Request $request, $id)
    {
        $mesa = Mesa::find($id);

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa no encontrada'
            ], 404);
        }

        // Validación de datos para la actualización, permitiendo campos opcionales y asegurando que el número de mesa sea único si se proporciona en la solicitud.
        $validated = $request->validate([
            'numero_mesa' => 'sometimes|integer|unique:Mesa,numero_mesa,' . $id . ',id_mesa',
            'capacidad' => 'sometimes|integer',
            'estado' => 'sometimes|in:Libre,Ocupada,Reservada,Mantenimiento'
        ]);

        $mesa->update($validated);

        return response()->json($mesa);
    }

    // Soft delete de una mesa, marcándola como eliminada sin eliminarla físicamente de la base de datos. Devuelve una respuesta JSON con un mensaje indicando que la mesa fue eliminada correctamente. Si la mesa no existe, devuelve un mensaje de error con un código de estado 404.
    public function destroy($id)
    {
        $mesa = Mesa::find($id);

        if (!$mesa) {
            // Si la mesa no existe, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
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
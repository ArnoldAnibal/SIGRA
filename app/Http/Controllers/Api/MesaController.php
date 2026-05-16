<?php

namespace App\Http\Controllers\Api;

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

    // Crear nueva mesa
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

    // Obtener una mesa específica
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

    // Actualizar mesa
    public function update(Request $request, $id)
    {
        $mesa = Mesa::find($id);

        if (!$mesa) {
            return response()->json([
                'message' => 'Mesa no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'numero_mesa' => 'sometimes|integer|unique:Mesa,numero_mesa,' . $id . ',id_mesa',
            'capacidad' => 'sometimes|integer',
            'estado' => 'sometimes|in:Libre,Ocupada,Reservada,Mantenimiento'
        ]);

        $mesa->update($validated);

        return response()->json($mesa);
    }

    // Soft delete
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
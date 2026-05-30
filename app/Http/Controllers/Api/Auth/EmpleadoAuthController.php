<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// Controlador para manejar las solicitudes relacionadas con la autenticación de empleados. Este controlador incluye métodos para el login, logout y obtener los datos del empleado autenticado. Utiliza el modelo Empleado para interactuar con la base de datos y el sistema de autenticación de Laravel Sanctum para manejar los tokens de acceso. El método login permite a los empleados iniciar sesión utilizando su nombre de usuario junto con su contraseña, mientras que el método logout revoca el token de acceso actual del empleado. El método me devuelve los datos del empleado autenticado actualmente.
class EmpleadoAuthController extends Controller
{
    /**
     * Login de empleados
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        $empleado = Empleado::where('username', $request->login)
            ->first();

        if (
            !$empleado ||
            !Hash::check($request->password, $empleado->password)
        ) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $empleado
            ->createToken('empleado_token')
            ->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'empleado' => [
                'id_empleado' => $empleado->id_empleado,
                'nombre' => $empleado->nombre,
                'rol' => $empleado->rol,
                'username' => $empleado->username
            ]
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout exitoso'
        ]);
    }

    /**
     * Usuario autenticado
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
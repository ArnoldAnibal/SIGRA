<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Cliente;

// Controlador para manejar las solicitudes relacionadas con la autenticación de clientes. Este controlador incluye métodos para el login, logout y obtener los datos del cliente autenticado. Utiliza el modelo Cliente para interactuar con la base de datos y el sistema de autenticación de Laravel Sanctum para manejar los tokens de acceso. El método login permite a los clientes iniciar sesión utilizando su nombre de usuario o correo electrónico junto con su contraseña, mientras que el método logout revoca el token de acceso actual del cliente. El método me devuelve los datos del cliente autenticado actualmente.
class ClienteAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN CLIENTE
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        // Buscar por username o email
        $cliente = Cliente::where('username', $request->login)
            ->orWhere('email', $request->login)
            ->first();

        // Cliente no encontrado
        if (!$cliente) {

            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // Password incorrecta
        if (!Hash::check(
            $request->password,
            $cliente->password
        )) {

            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // Cliente suspendido
        if ($cliente->estado === 'Suspendido') {

            return response()->json([
                'message' => 'Cliente suspendido'
            ], 403);
        }

        // Crear token
        $token = $cliente->createToken(
            'cliente_token'
        )->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'cliente' => [
                'id_cliente' => $cliente->id_cliente,
                'nombre' => $cliente->nombre,
                'username' => $cliente->username,
                'email' => $cliente->email,
                'tipo_cliente' => $cliente->tipo_cliente
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT CLIENTE
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logout exitoso'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CLIENTE AUTENTICADO
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {
        return response()->json([
            'cliente' => $request->user()
        ]);
    }
}
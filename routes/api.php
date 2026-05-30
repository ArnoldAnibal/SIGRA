<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoriaMenuController;
use App\Http\Controllers\Api\ProductoMenuController;
use App\Http\Controllers\Api\MesaController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\EmpleadoController;
use App\Http\Controllers\Api\PlanillaController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\FacturaElectronicaController;
use App\Http\Controllers\Api\DetallePedidoController;
use App\Http\Controllers\Api\AsistenciaController;
use App\Http\Controllers\Api\PagoController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\CuentaPorPagarController;
use App\Http\Controllers\Api\InventarioMovimientoController;
use App\Http\Controllers\Api\ClienteMetodoPagoController;
use App\Http\Controllers\Api\Auth\EmpleadoAuthController;
use App\Http\Controllers\Api\Auth\ClienteAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH EMPLEADOS
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth/empleados')->group(function () {

        Route::post('/login', [
            EmpleadoAuthController::class,
            'login'
        ]);

        Route::middleware('auth:sanctum')->group(function () {

            Route::post('/logout', [
                EmpleadoAuthController::class,
                'logout'
            ]);

            Route::get('/me', [
                EmpleadoAuthController::class,
                'me'
            ]);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | AUTH CLIENTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth/clientes')->group(function () {

        Route::post('/login', [
            ClienteAuthController::class,
            'login'
        ]);

        Route::middleware('auth:sanctum')->group(function () {

            Route::post('/logout', [
                ClienteAuthController::class,
                'logout'
            ]);

            Route::get('/me', [
                ClienteAuthController::class,
                'me'
            ]);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS PUBLICAS
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'productos-menu',
        ProductoMenuController::class
    )->only(['index', 'show']);

    Route::apiResource(
        'categorias-menu',
        CategoriaMenuController::class
    )->only(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | RUTAS PROTEGIDAS EMPLEADOS
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:Administrador')->group(function () {

    Route::apiResource(
        'empleados',
        EmpleadoController::class
    );

    Route::apiResource(
        'planillas',
        PlanillaController::class
    );

    Route::apiResource(
        'proveedores',
        ProveedorController::class
    );

    Route::apiResource(
        'cuentas-por-pagar',
        CuentaPorPagarController::class
    );

    Route::apiResource(
        'inventario-movimientos',
        InventarioMovimientoController::class
    );
});

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR + CAJERO
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:Administrador,Cajero')->group(function () {

            Route::apiResource(
                'clientes',
                ClienteController::class
            );

            Route::apiResource(
                'facturas-electronicas',
                FacturaElectronicaController::class
            );

            Route::apiResource(
                'pagos',
                PagoController::class
            );

            Route::apiResource(
                'cliente-metodos-pago',
                ClienteMetodoPagoController::class
            );
        });

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR + MESERO + CAJERO + COCINERO
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:Administrador,Mesero,Cajero,Cocinero')->group(function () {

            Route::apiResource(
                'mesas',
                MesaController::class
            );

            Route::apiResource(
                'pedidos',
                PedidoController::class
            );

            Route::apiResource(
                'detalles-pedido',
                DetallePedidoController::class
            );
        });

        /*
        |--------------------------------------------------------------------------
        | CLOCK OUT PERSONALIZADO
        |--------------------------------------------------------------------------
        */

        Route::put(
            '/asistencias/cerrar-jornada',
            [AsistenciaController::class, 'cerrarJornada']
        );

        /*
        |--------------------------------------------------------------------------
        | ASISTENCIAS
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'asistencias',
            AsistenciaController::class
        );

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS Y CATEGORIAS ADMIN
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:Administrador')->group(function () {

            Route::apiResource(
                'productos-menu',
                ProductoMenuController::class
            )->except(['index', 'show']);

            Route::apiResource(
                'categorias-menu',
                CategoriaMenuController::class
            )->except(['index', 'show']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS PROTEGIDAS CLIENTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | PERFIL CLIENTE
        |--------------------------------------------------------------------------
        */

        Route::get('/clientes/me', function (Request $request) {

            return response()->json([
                'cliente' => auth()->user()
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | PEDIDOS DEL CLIENTE
        |--------------------------------------------------------------------------
        */

        Route::get('/clientes/mis-pedidos', function () {

            $cliente = auth()->user();

            return response()->json([
                'data' => $cliente->pedidos
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | FACTURAS DEL CLIENTE
        |--------------------------------------------------------------------------
        */

        Route::get('/clientes/mis-facturas', function () {

            $cliente = auth()->user();

            return response()->json([
                'data' => $cliente->facturasCredito
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | SALDO CREDITO
        |--------------------------------------------------------------------------
        */

        Route::get('/clientes/credito', function () {

            $cliente = auth()->user();

            if ($cliente->tipo_cliente !== 'Empresa') {

                return response()->json([
                    'message' => 'Solo clientes empresa poseen crédito.'
                ], 403);
            }

            return response()->json([
                'limite_credito' => $cliente->limite_credito,
                'saldo_credito_actual' => $cliente->saldo_credito_actual,
                'credito_disponible' =>
                    $cliente->limite_credito -
                    $cliente->saldo_credito_actual
            ]);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | USER AUTH TEST
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

        return $request->user();
    });

    /*
    |--------------------------------------------------------------------------
    | FALLBACK
    |--------------------------------------------------------------------------
    */

    Route::fallback(function () {

        return response()->json([
            'message' => 'Ruta no encontrada. Verifique la URL de la API.'
        ], 404);
    });

});
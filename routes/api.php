<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Importamos el controlador CategoriaMenuController y ProductoMenuController para poder utilizarlos en las rutas de la API relacionadas con las categorías y productos de menú. También importamos el controlador MesaController para manejar las rutas relacionadas con las mesas. Estos controladores contienen los métodos necesarios para realizar las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) en las respectivas entidades de la aplicación.
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

// Definimos un grupo de rutas con el prefijo 'v1' para versionar nuestra API. Dentro de este grupo, registramos un recurso API para las categorías de menú utilizando el controlador CategoriaMenuController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con las categorías de menú.
Route::prefix('v1')->group(function () {


    // Registramos un recurso API para los productos de menú utilizando el controlador ProductoMenuController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con los productos de menú. Esto permite a los clientes de la API interactuar con los productos de menú a través de las rutas definidas, facilitando la gestión de los productos en la aplicación.
    Route::apiResource(
        'categorias-menu',
        CategoriaMenuController::class
    );

    // Registramos un recurso API para los productos de menú utilizando el controlador ProductoMenuController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con los productos de menú. Esto permite a los clientes de la API interactuar con los productos de menú a través de las rutas definidas, facilitando la gestión de los productos en la aplicación.
    Route::apiResource(
        'productos-menu',
        ProductoMenuController::class
    );

    // Registramos un recurso API para las mesas utilizando el controlador MesaController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con las mesas. Esto permite a los clientes de la API interactuar con las mesas a través de las rutas definidas, facilitando la gestión de las mesas en la aplicación.
    Route::apiResource(
        'mesas',
        MesaController::class
    );

    // Registramos un recurso API para los clientes utilizando el controlador ClienteController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con los clientes. Esto permite a los clientes de la API interactuar con los clientes a través de las rutas definidas, facilitando la gestión de los clientes en la aplicación.
    Route::apiResource(
        'clientes',
        ClienteController::class
    );

    // Registramos un recurso API para los empleados utilizando el controlador EmpleadoController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con los empleados. Esto permite a los clientes de la API interactuar con los empleados a través de las rutas definidas, facilitando la gestión de los empleados en la aplicación.
    Route::apiResource(
        'empleados',
        EmpleadoController::class
    );

    // Registramos un recurso API para las planillas utilizando el controlador PlanillaController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con las planillas. Esto permite a los clientes de la API interactuar con las planillas a través de las rutas definidas, facilitando la gestión de las planillas en la aplicación.
    Route::apiResource(
        'planillas',
        PlanillaController::class
    );

    // Registramos un recurso API para los pedidos utilizando el controlador PedidoController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con los pedidos. Esto permite a los clientes de la API interactuar con los pedidos a través de las rutas definidas, facilitando la gestión de los pedidos en la aplicación.
    Route::apiResource(
        'pedidos',
        PedidoController::class
    );

    // Registramos un recurso API para las facturas electrónicas utilizando el controlador FacturaElectronicaController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con las facturas electrónicas. Esto permite a los clientes de la API interactuar con las facturas electrónicas a través de las rutas definidas, facilitando la gestión de las facturas en la aplicación.
    Route::apiResource(
        'facturas-electronicas',
        FacturaElectronicaController::class
    );

    // Registramos un recurso API para los detalles de pedido utilizando el controlador DetallePedidoController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con los detalles de pedido. Esto permite a los clientes de la API interactuar con los detalles de pedido a través de las rutas definidas, facilitando la gestión de los detalles de pedido en la aplicación.
    Route::apiResource(
        'detalles-pedido',
        DetallePedidoController::class
    );

    Route::apiResource(
        'pagos',
        PagoController::class
    );

    // Registramos un recurso API para las asistencias utilizando el controlador AsistenciaController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con las asistencias. Esto permite a los clientes de la API interactuar con las asistencias a través de las rutas definidas, facilitando la gestión de las asistencias en la aplicación.
    Route::apiResource(
        'asistencias',
        AsistenciaController::class
    );

    // Ruta para registrar la salida (clock out) de un empleado. Esta ruta utiliza el método HTTP PUT y está asociada al método cerrarJornada del AsistenciaController. El {id_empleado} es un parámetro de ruta que representa el ID del empleado para el cual se desea registrar la salida. Al acceder a esta ruta, se ejecutará la lógica definida en el método cerrarJornada del controlador, que se encargará de actualizar la asistencia del empleado correspondiente para marcar su salida.
    Route::put(
    '/asistencias/empleado/{id_empleado}',
    [AsistenciaController::class, 'cerrarJornada']
);

});

// Ruta protegida por autenticación que devuelve los datos del usuario autenticado. Esta ruta utiliza el middleware 'auth:sanctum' para asegurar que solo los usuarios autenticados puedan acceder a ella. Si el usuario está autenticado, se devuelve su información en formato JSON.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Ruta no encontrada. Verifique la URL de la API.'
    ], 404);
});

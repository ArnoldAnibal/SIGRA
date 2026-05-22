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

// Definimos un grupo de rutas con el prefijo 'v1' para versionar nuestra API. Dentro de este grupo, registramos un recurso API para las categorías de menú utilizando el controlador CategoriaMenuController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con las categorías de menú.
Route::prefix('v1')->group(function () {


    // Registramos un recurso API para los productos de menú utilizando el controlador ProductoMenuController, lo que automáticamente genera las rutas para las operaciones CRUD (index, show, store, update, destroy) relacionadas con los productos de menú. Esto permite a los clientes de la API interactuar con los productos de menú a través de las rutas definidas, facilitando la gestión de los productos en la aplicación.
    Route::apiResource(
        'categorias-menu',
        CategoriaMenuController::class
    );

    Route::apiResource(
        'productos-menu',
        ProductoMenuController::class
    );

    Route::apiResource(
        'mesas',
        MesaController::class
    );

});

// Ruta protegida por autenticación que devuelve los datos del usuario autenticado. Esta ruta utiliza el middleware 'auth:sanctum' para asegurar que solo los usuarios autenticados puedan acceder a ella. Si el usuario está autenticado, se devuelve su información en formato JSON.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

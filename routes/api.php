<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Users\UsuarioController;
use App\Http\Middleware\CheckUserType;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', [UsuarioController::class, 'getUsuarios'])->middleware(['checkUserType:empleado']);
Route::get('/users/{id}',[UsuarioController::class, 'getUsuario']);
Route::post('/users',[UsuarioController::class, 'store'] );
Route::put('/users/{id}',[UsuarioController::class, 'update']);
Route::delete('/users/{id}',[UsuarioController::class, 'delete']);
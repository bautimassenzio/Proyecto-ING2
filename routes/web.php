<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\ClienteController;
use App\Http\Controllers\Web\UsuarioController;
use App\Http\Controllers\Web\ViewsController;

Route::get('/', [ViewsController::class, 'vistaInicio'])->name('/');

Route::get('/register', [ViewsController::class, 'vistaRegistro']);
Route::post('/register', [ClienteController::class, 'storeClient'])->name('register');

Route::get('/registerByEmployee', [ViewsController::class, 'vistaRegistroPorEmpleado']);
Route::post('/registerByEmployee', [ClienteController::class, 'crearContraseña'])->name('registerByEmployee');

Route::get('/passwordReset', [ViewsController::class, 'vistaCambioContraseña'])->name('passwordReset');
Route::post('/passwordReset', [UsuarioController::class, 'updatePassword'])->name('passwordReset');

Route::post('/reenviarCodigo', [AdminController::class, 'reenviarCodigo'])->name('reenviarCodigo');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/confirmarAdmin', [ViewsController::class, 'vistaConfirmarAdmin']);
Route::post('/confirmarAdmin', [AdminController::class, 'CodigoVerificacionMail'])->name('confirmarAdmin');


Route::get('/exitoRegister', [ViewsController::class, 'exitoRegister']);

Route::get('/eliminarCuenta', [ViewsController::class, 'vistaEliminarCuenta'])->middleware('checkUserType:cliente')->name('eliminarCuenta');
Route::delete('/eliminarCuenta', [UsuarioController::class, 'eliminarCuentaPropia'])->middleware('checkUserType:cliente')->name('eliminarCuentaPost');

Route::middleware(['checkUserType:empleado,admin'])->group(function () {
    Route::get('/users', [UsuarioController::class, 'getUsuarios']);
    Route::get('/users/{id}',[UsuarioController::class, 'getUsuario']);
    Route::post('/users',[UsuarioController::class, 'store'] );
    Route::put('/users/{id}',[UsuarioController::class, 'update']);
    Route::delete('/users/{id}',[UsuarioController::class, 'delete']);
});



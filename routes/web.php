<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\Reservas\ReservaController;
use App\Http\Controllers\Web\Pago\PagoController;
use App\Http\Controllers\Web\Maquinarias\MaquinariaController;
use App\Http\Controllers\Web\Users\AdminController;
use App\Http\Controllers\Web\Users\ClienteController;
use App\Http\Controllers\Web\Users\UsuarioController;
use App\Http\Controllers\Web\Users\ViewsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ViewsController::class, 'vistaWelcome']);
Route::get('/inicio', [ViewsController::class, 'vistaInicio'])->name('inicio');
Route::get('/fail', [ViewsController::class, 'vistaInicioFallido']);


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


Route::get('/users', [UsuarioController::class, 'getUsuarios'])->middleware(['checkUserType:cliente']);

Route::prefix('reservas')->group(function () {
    Route::get('/crear', [ReservaController::class, 'create'])->name('reservas.create');
    Route::post('/store', [ReservaController::class, 'store'])->name('reservas.store');
});


//MERCADOPAGO
Route::get('/pagar', function() {
    return view('pago.seleccionarpago');
})->name('pago.seleccionar');


Route::prefix('pago')->group(function () {
  
    Route::get('/exito', [PagoController::class, 'exito'])->name('pago.exito');
    Route::get('/fallo', [PagoController::class, 'fallo'])->name('pago.fallo');
    Route::get('/pendiente', [PagoController::class, 'pendiente'])->name('pago.pendiente');
});

Route::post('/pagar', [PagoController::class, 'pagar'])->name('pago.procesar');


Route::prefix('admin')->group(function () {
    // Rutas para la creación y guardado de maquinarias (solo para administradores)
    Route::get('/maquinarias/create', [MaquinariaController::class, 'create'])->name('maquinarias.create');
    Route::post('/maquinarias', [MaquinariaController::class, 'store'])->name('maquinarias.store');

    // Agrega rutas para editar, actualizar, eliminar si lo necesitas más adelante, y también estarán protegidas:
    // Route::get('/maquinarias/{maquinaria}/edit', [MaquinariaController::class, 'edit'])->name('maquinarias.edit');
    // Route::put('/maquinarias/{maquinaria}', [MaquinariaController::class, 'update'])->name('maquinarias.update');
    // Route::delete('/maquinarias/{maquinaria}', [MaquinariaController::class, 'destroy'])->name('maquinarias.destroy');
});
    Route::get('admin/maquinarias', [MaquinariaController::class, 'index'])->name('maquinarias.index');
// ...
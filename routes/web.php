<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\Reservas\ReservaController;
use App\Http\Controllers\Web\Pago\PagoController;

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

Route::get('/', function () {return view('welcome');
});

Route::get('/inicio', function () {return view('inicioEjemplo');
});

Route::get('/fail', function () {return view('inicioEjemploFail');
});


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::prefix('reservas')->group(function () {
    Route::get('/crear', [ReservaController::class, 'create'])->name('reservas.create');
    Route::post('/store', [ReservaController::class, 'store'])->name('reservas.store');
});


//MERCADOPAGO
Route::get('/pagar', function() {
    return view('pago.seleccionarpago');
})->name('pago.seleccionar');

Route::get('/pago/exito', [PagoController::class, 'exito'])->name('pago.exito');
Route::get('/pago/fallo', [PagoController::class, 'fallo'])->name('pago.fallo');
Route::get('/pago/pendiente', [PagoController::class, 'pendiente'])->name('pago.pendiente');

Route::post('/pagar', [PagoController::class, 'pagar'])->name('pago.procesar');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\ClienteController;
use App\Http\Controllers\Web\UsuarioController;
use App\Http\Controllers\Web\ViewsController;

Route::get('/', [ViewsController::class, 'vistaWelcome']);
Route::get('/inicio', [ViewsController::class, 'vistaInicio']);
Route::get('/fail', [ViewsController::class, 'vistaInicioFallido']);


Route::get('/register', function () {return view('registrarCliente');});
Route::post('/register', [ClienteController::class, 'storeClient'])->name('register');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/users', [UsuarioController::class, 'getUsuarios'])->middleware(['checkUserType:empleado']);

//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Session;
// Route::get('/debug-session', function () {
//     return response()->json([
//         'usuario_autenticado' => Auth::guard('users')->check(),
//         'usuario' => Auth::guard('users')->user(),
//         'session_id' => Session::getId(),
//         'session_data' => Session::all(),
//         'cookie_laravel_session' => request()->cookie('laravel_session'),
//     ]);
// });

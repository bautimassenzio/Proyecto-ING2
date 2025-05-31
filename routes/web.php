<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Maq\MaquinariaController; // Asegúrate de importar el controlador

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


// ... otras rutas

Route::prefix('admin')->middleware(['auth', 'checkUserType:administrador'])->group(function () {
    // Rutas para la creación y guardado de maquinarias (solo para administradores)
    Route::get('/maquinarias/create', [MaquinariaController::class, 'create'])->name('maquinarias.create');
    Route::post('/maquinarias', [MaquinariaController::class, 'store'])->name('maquinarias.store');

    // Agrega rutas para editar, actualizar, eliminar si lo necesitas más adelante, y también estarán protegidas:
    // Route::get('/maquinarias/{maquinaria}/edit', [MaquinariaController::class, 'edit'])->name('maquinarias.edit');
    // Route::put('/maquinarias/{maquinaria}', [MaquinariaController::class, 'update'])->name('maquinarias.update');
    //Route::delete('/maquinarias/{maquinaria}', [MaquinariaController::class, 'destroy'])->name('maquinarias.destroy');
});
    Route::delete('admin/maquinarias/{maquinaria}', [MaquinariaController::class, 'destroy'])->name('maquinarias.destroy');
    Route::get('admin/maquinarias', [MaquinariaController::class, 'index'])->name('maquinarias.index');
// ...
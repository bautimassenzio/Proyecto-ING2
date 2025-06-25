<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\Reservas\ReservaController;
use App\Http\Controllers\Web\Pago\PagoController;
use App\Http\Controllers\Web\Maquinarias\MaquinariaController;
use App\Http\Controllers\Web\Users\AdminController;
use App\Http\Controllers\Web\Users\ClienteController;
use App\Http\Controllers\Web\Users\UsuarioController;
use App\Http\Controllers\Web\Users\ViewsController; //Controller donde se redirecciona a las vistas
use App\Http\Controllers\Web\Estadisticas\EstadisticaController;
// Vista Inicio
Route::get('/', [ViewsController::class, 'vistaInicio'])->name('/');

// Operaciones de registro
Route::get('/register', [ViewsController::class, 'vistaRegistro']);
Route::post('/register', [ClienteController::class, 'storeClient'])->name('register');
Route::get('/registerByEmployee', [ViewsController::class, 'vistaRegistroPorEmpleado']);
Route::post('/registerByEmployee', [ClienteController::class, 'crearContraseña'])->name('registerByEmployee');

Route::get('/passwordReset', [ViewsController::class, 'vistaCambioContraseña'])->name('passwordReset')->middleware('auth:users');
Route::post('/passwordReset', [UsuarioController::class, 'updatePassword'])->name('passwordReset')->middleware('auth:users');

// Login y Logout
Route::get('/login', [ViewsController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth:users');;

// Confirmacion de admin via Mail
Route::get('/confirmarAdmin', [ViewsController::class, 'vistaConfirmarAdmin']);
Route::post('/confirmarAdmin', [AdminController::class, 'CodigoVerificacionMail'])->name('confirmarAdmin');
Route::post('/reenviarCodigo', [AdminController::class, 'reenviarCodigo'])->name('reenviarCodigo');

Route::get('/exitoRegister', [ViewsController::class, 'exitoRegister']);

// Eliminacion de cuenta
Route::get('/eliminarCuenta', [ViewsController::class, 'vistaEliminarCuenta'])->middleware('checkUserType:cliente')->name('eliminarCuenta');
Route::delete('/eliminarCuenta', [ClienteController::class, 'eliminarCuentaPropia'])->middleware('checkUserType:cliente')->name('eliminarCuentaPost');

// Operaciones que solo pueden realizar empleado y admin
Route::middleware(['checkUserType:empleado,admin'])->group(function () {
    Route::get('/users', [UsuarioController::class, 'getUsuarios']);
    Route::get('/users/{id}',[UsuarioController::class, 'getUsuario']);
    Route::post('/users',[UsuarioController::class, 'store'] );
    Route::put('/users/{id}',[UsuarioController::class, 'update']);
    Route::delete('/users/{id}',[UsuarioController::class, 'delete']);
});


// Crear y guardar reservas
Route::prefix('reservas')->middleware('auth:users')->group(function () {
    Route::get('/crear', [ReservaController::class, 'create'])->name('reservas.create');
    Route::post('/', [ReservaController::class, 'store'])->name('reservas.store');
    Route::post('/{id_reserva}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');
    Route::get('/reservas/{id_reserva}/pagar', [ReservaController::class, 'pagarDesdeHistorial'])->name('reservas.pagarDesdeHistorial');
});

// Historial de reservas del cliente
Route::get('/mis-reservas', [ReservaController::class, 'index'])
    ->name('reservas.index')
    ->middleware('auth:users');


// MercadoPago
Route::get('/pagar', function() {
    $layout=session('layout','layouts.base');
    return view('pago.seleccionarpago', compact('layout'));
})->middleware('auth:users')->name('pago.seleccionar');

Route::prefix('pago')->group(function () {
    Route::get('/exito', [PagoController::class, 'exito'])->name('pago.exito')->middleware('auth:users');
    Route::get('/fallo', [PagoController::class, 'fallo'])->name('pago.fallo')->middleware('auth:users');;
    Route::get('/pendiente', [PagoController::class, 'pendiente'])->name('pago.pendiente')->middleware('auth:users');
});

Route::post('/pagar', [PagoController::class, 'pagar'])->name('pago.procesar')->middleware('auth:users');


// Rutas para la creación y guardado de maquinarias (solo para administradores)
Route::prefix('admin')->group(function () {
    Route::get('/maquinarias/create', [MaquinariaController::class, 'create'])->name('maquinarias.create');
    Route::post('/maquinarias', [MaquinariaController::class, 'store'])->name('maquinarias.store');

});

// Edicion de maquinarias
Route::get('admin/maquinarias/{maquinaria}/edit', [MaquinariaController::class, 'edit'])->name('maquinarias.edit');
Route::put('admin/maquinarias/{maquinaria}', [MaquinariaController::class, 'update'])->name('maquinarias.update');

// Eliminar Maquinaria
Route::delete('admin/maquinarias/{maquinaria}', [MaquinariaController::class, 'destroy'])->name('maquinarias.destroy');
//Route::get('admin/maquinarias', [MaquinariaController::class, 'index'])->name('maquinarias.index');

// Visualizar catalogo y maquinaria especifica
Route::get('/catalogo', [MaquinariaController::class, 'index'])->name('catalogo.index'); // Catálogo Adaptativo
Route::get('/catalogo/{maquinaria}', [MaquinariaController::class, 'show'])->name('catalogo.show'); // Detalle Adaptativo


// Visualizar informacion
Route::get('/info-contactos', [ViewsController::class, 'mostrarInformacionContacto'])->name('info.contactos');
Route::get('/preguntas-frecuentes', [ViewsController::class, 'mostrarPreguntasFrecuentes'])->name('preguntas.frecuentes');


// Pagos con tarjeta
Route::get('/procesar-pago/tarjeta', [PagoController::class, 'mostrarFormularioTarjeta'])->name('pago.procesar.tarjeta');
Route::post('/procesar-pago/tarjeta', [PagoController::class, 'procesarPagoTarjeta'])->name('procesar.pago.tarjeta');


// Estadisticas
Route::get('/estadisticas', [EstadisticaController::class, 'showStatistics'])->name('admin.estadisticas')->middleware('checkUserType:admin');
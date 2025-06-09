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
use App\Http\Controllers\Web\Visualizar\VisualizarController;

Route::get('/register', [ViewsController::class, 'vistaRegistro']);
Route::post('/register', [ClienteController::class, 'storeClient'])->name('register');

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


// Crear y guardar reservas
Route::prefix('reservas')->middleware('auth:users')->group(function () {
    Route::get('/crear', [ReservaController::class, 'create'])->name('reservas.create');
    Route::post('/', [ReservaController::class, 'store'])->name('reservas.store');
    Route::post('/{id_reserva}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');
});

// Historial de reservas del cliente
Route::get('/mis-reservas', [ReservaController::class, 'index'])
    ->name('reservas.index')
    ->middleware('auth:users');


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
    
});
        // Ruta para mostrar el formulario de edición de una maquinaria específica
    Route::get('admin/maquinarias/{maquinaria}/edit', [MaquinariaController::class, 'edit'])->name('maquinarias.edit');

    // Ruta para procesar la actualización de una maquinaria (método PUT/PATCH)
    Route::put('admin/maquinarias/{maquinaria}', [MaquinariaController::class, 'update'])->name('maquinarias.update');
    Route::delete('admin/maquinarias/{maquinaria}', [MaquinariaController::class, 'destroy'])->name('maquinarias.destroy');
    //Route::get('admin/maquinarias', [MaquinariaController::class, 'index'])->name('maquinarias.index');
     Route::get('/catalogo', [MaquinariaController::class, 'index'])->name('catalogo.index'); // Catálogo Adaptativo
    Route::get('/catalogo/{maquinaria}', [MaquinariaController::class, 'show'])->name('catalogo.show'); // Detalle Adaptativo
// ...

Route::get('/info-contactos', [VisualizarController::class, 'mostrarInformacionContacto'])->name('info.contactos');
Route::get('/preguntas-frecuentes', [VisualizarController::class, 'mostrarPreguntasFrecuentes'])->name('preguntas.frecuentes');

Route::get('/procesar-pago/tarjeta', [PagoController::class, 'mostrarFormularioTarjeta'])->name('pago.procesar.tarjeta');
Route::post('/procesar-pago/tarjeta', [PagoController::class, 'procesarPagoTarjeta'])->name('procesar.pago.tarjeta');


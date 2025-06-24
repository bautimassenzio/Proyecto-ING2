<?php

namespace App\Http\Controllers\Web\Users;

use Illuminate\Http\Request;
use App\Domain\User\Models\Usuario;
use App\Enums\Roles;
use App\Enums\Estados;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Mail\EnviarContraseña;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Domain\Reserva\Models\Reserva;

class ClienteController extends Controller
{
    
    // Funcion para verificar la validez de los datos al registrar un cliente, si son correctos se almacena el cliente
    public function storeClient(Request $request)
    {

        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:usuarios,email',
            'contraseña' => 'required|string|min:4',
            'dni' => 'required|string|unique:usuarios,dni|regex:/^\d{7,8}$/',
            'telefono' => 'required|string',
            'fecha_nacimiento' => ['required', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],
        ],[
        
            'nombre.required' => 'El nombre es obligatorio.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',

            'contraseña.required' => 'La contraseña es obligatoria.',
            'contraseña.string' => 'La contraseña debe ser una cadena de texto.',
            'contraseña.min' => 'La contraseña debe tener al menos 6 caracteres.',

            'dni.required' => 'El DNI es obligatorio.',
            'dni.string' => 'El DNI debe ser una cadena de texto.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'dni.regex' => 'El DNI debe tener 7 u 8 dígitos, sin letras ni caracteres especiales.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.string' => 'El teléfono debe ser una cadena de texto.',

            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before' => 'No se pueden registrar usuarios menores a 18 años'

        ]);
    
        // Crear usuario de tipo cliente
        $this->crearCliente($request);
        
        if (Auth::guard('users')->check() && Auth::guard('users')->user()->rol == Roles::EMPLEADO->value) {
            // Si el cliente fue registrado por un empleado solo se informa que la operacion finalizo, no redirige el empleado al login
            return back()->with('success', 'Operación realizada correctamente.');  
        }
            return redirect('/exitoRegister');
    }

    // Almacena un usuario con rol cliente
    public function crearCliente($request){
        $rol=Roles::CLIENTE;
        $estado=Estados::ACTIVO;
        return Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'contraseña' => bcrypt($request->contraseña),
            'rol' => $rol,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'estado' => $estado,  
            'fecha_nacimiento' => $request->fecha_nacimiento,      
            'fecha_alta' => now(),
        ]);
    }

    // genera una contraseña aleatoria de 8 caracteres y se la asigna al nuevo usuario creado
    public function crearContraseña(Request $request){
        $contraseñaGenerada = Str::random(8); 
        $request->merge([
            'contraseña' => $contraseñaGenerada
        ]);
        $response = $this->storeClient($request);
        Mail::to($request->email)->send(new EnviarContraseña($request->nombre, $contraseñaGenerada)); //Envia un mail al nuevo usuario con su contraseña
        return $response;
    }

    // Eliminacion de cuenta de un cliente sin reservas activas
    public function eliminarCuentaPropia()
{
    $usuario = Auth::guard('users')->user();
    $rol=$usuario->rol;
    if ($rol=='cliente' && Reserva::where('id_cliente', $usuario->id_usuario)
            ->whereIn('estado', ['aprobada', 'activa'])
            ->exists()){ // Verifico que no tenga reservas activas
        return back()->with('error', 'No se puede eliminar una cuenta con reservas activas');
    }
    Auth::guard('users')->logout(); // cierra sesión
    UsuarioController::logicDelete($usuario->email); //Borrado logico, se implementa en usuario ya que tambien se pueden borrar empleados
    session()->forget('layout');
    return redirect('/')->with('success', 'Tu cuenta fue eliminada correctamente.');
}


}
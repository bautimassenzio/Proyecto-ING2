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
class EmpleadoController extends Controller // Logica para crear clientes, desde empleado o cuenta propia
{

    //Almacena un empleado desde la vista del admin
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:usuarios,email',
            'dni' => 'required|string|unique:usuarios,dni|regex:/^\d{7,8}$/',
            'telefono' => 'required|string',
            'fecha_nacimiento' => ['required', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],
        ],[
        
            'nombre.required' => 'El nombre es obligatorio.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',

            'dni.required' => 'El DNI es obligatorio.',
            'dni.string' => 'El DNI debe ser una cadena de texto.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'dni.regex' => 'El DNI debe tener 7 u 8 dígitos, sin letras ni caracteres especiales.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.string' => 'El teléfono debe ser una cadena de texto.',

            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before' => 'No se pueden registrar usuarios menores a 18 años'

        ]);
        $this->crearContraseña($request);
        return back()->with('success', 'Operación realizada correctamente.'); 
    }



    // Almacena un usuario con rol cliente
    public function crearEmpleado($request){
        $rol=Roles::EMPLEADO;
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
        $response = $this->crearEmpleado($request);
        Mail::to($request->email)->send
        (new EnviarContraseña($request->nombre, $contraseñaGenerada)); //Envia un mail al nuevo usuario con su contraseña
        return $response;
    }
}
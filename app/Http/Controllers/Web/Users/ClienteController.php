<?php

namespace App\Http\Controllers\Web\Users;

use Illuminate\Http\Request;
use App\Domain\User\Models\Usuario;
use App\Enums\Roles;
use App\Enums\Estados;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Mail\EnviarContraseña;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class ClienteController extends Controller
{
    public function storeClient(Request $request)
    {

        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:usuarios,email',
            'contraseña' => 'required|string|min:4',
            'dni' => 'required|string|unique:usuarios,dni|regex:/^\d{7,8}$/',
            'telefono' => 'required|string',
            'fecha_nacimiento' => 'required|date'
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

        ]);
    
        // Crear usuario
        if (! $this->mayor18($request->fecha_nacimiento)) return back()->withErrors(['mensaje' => 'No se pueden registrar usuarios menores a 18 años']);

        $this->crearUsuario($request);
    
        return redirect('/exitoRegister');
    }

    public function mayor18($fecha_nacimiento){
        $fechaNacimiento=Carbon::parse($fecha_nacimiento);
        return $fechaNacimiento->age >= 18;
    }

    public function crearUsuario($request){
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

    public function crearContraseña(Request $request){
        $contraseñaGenerada = Str::random(8); // genera una contraseña aleatoria de 8 caracteres
        $request->merge([
            'contraseña' => $contraseñaGenerada
        ]);
        $this->storeClient($request);
        Mail::to($request->email)->send(new EnviarContraseña($request->nombre, $contraseñaGenerada));
    }
}
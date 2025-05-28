<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Domain\User\Models\Usuario;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Mail\EnviarContraseña;
use Illuminate\Support\Facades\Mail;

class ClienteController extends Controller
{
    public function storeClient(Request $request)
    {
        // Validación básica (opcional pero recomendable)
        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:usuarios,email',
            'contraseña' => 'required|string|min:4',
            'dni' => 'required|string|unique:usuarios,dni',
            'telefono' => 'required|string',
            'estado' => 'required|string',
            'fecha_alta' => 'required|date',
        ]);
    
        // Crear usuario
        $usuario = $this->crearUsuario($request);
    
        return response()->json(['mensaje' => 'Usuario creado con éxito', 'usuario' => $usuario], 201);
    }

    public function crearUsuario($request){
        $rol=Roles::CLIENTE;
        return Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'contraseña' => bcrypt($request->contraseña),
            'rol' => $rol,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'estado' => $request->estado,
            'fecha_alta' => $request->fecha_alta,
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

<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Domain\User\Models\Usuario;
use App\Enums\Roles;
use App\Http\Controllers\Controller;

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
        $rol=Roles::CLIENTE;
        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'contraseña' => bcrypt($request->contraseña),
            'rol' => $rol,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'estado' => $request->estado,
            'fecha_alta' => $request->fecha_alta,
        ]);
    
        return response()->json(['mensaje' => 'Usuario creado con éxito', 'usuario' => $usuario], 201);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Domain\User\Models\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\RolUsuario;

class UsuarioController extends Controller
{
    //

    //public function __construct(){
     // $this->middleware('auth:usuarios');
   //}

    public function getUsuarios(){
        return Usuario::all();
    }

    public function getUsuario($dni){
        $usuario = Usuario::where('dni', $dni)->first(); //Busqueda por DNI
        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario);
    }

  // Crear usuario (POST)
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:usuarios,email',
            'contraseña' => 'required|string|min:4',
            'rol' => 'required|string',
            'dni' => 'required|string|unique:usuarios,dni',
            'telefono' => 'required|string',
            'estado' => 'required|string',
            'fecha_alta' => 'required|date',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'contraseña' => bcrypt($request->contraseña),
            'rol' => $request->rol,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'estado' => $request->estado,
            'fecha_alta' => $request->fecha_alta,
        ]);

        return response()->json(['mensaje' => 'Usuario creado con éxito', 'usuario' => $usuario], 201);
    }

    // Obtener usuario por DNI (GET)
    public function show($dni)
    {
        $usuario = Usuario::where('dni', $dni)->first();

        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

    // Actualizar usuario por DNI (PUT)
    public function update(Request $request, $dni)
    {
        $usuario = Usuario::where('dni', $dni)->first();

        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'string',
            'email' => 'email|unique:usuarios,email,' . $usuario->id,
            'contraseña' => 'string|min:4',
            'rol' => 'string',
            'telefono' => 'string',
            'estado' => 'string',
            'fecha_alta' => 'date',
        ]);

        $usuario->update([
            'nombre' => $request->nombre ?? $usuario->nombre,
            'email' => $request->email ?? $usuario->email,
            'contraseña' => $request->contraseña ? bcrypt($request->contraseña) : $usuario->contraseña,
            'rol' => $request->rol ?? $usuario->rol,
            'telefono' => $request->telefono ?? $usuario->telefono,
            'estado' => $request->estado ?? $usuario->estado,
            'fecha_alta' => $request->fecha_alta ?? $usuario->fecha_alta,
        ]);

        return response()->json(['mensaje' => 'Usuario actualizado con éxito', 'usuario' => $usuario]);
    }

    // Eliminar usuario por DNI (DELETE)
    public function delete($dni)
    {
        $usuario = Usuario::where('dni', $dni)->first();

        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['mensaje' => 'Usuario eliminado con éxito']);
    }


}

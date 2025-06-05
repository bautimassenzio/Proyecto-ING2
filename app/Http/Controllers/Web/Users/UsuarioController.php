<?php

namespace App\Http\Controllers\Web\Users;

use App\Domain\Reserva\Models\Reserva;
use App\Domain\User\Models\Usuario;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\RolUsuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UsuarioController extends Controller
{


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
    public function update(Request $request, $email)
    {
        $usuario = Usuario::where('email', $email)->first();

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

    public function updatePassword(Request $request){
        $request->validate([
    'password_actual' => 'required',
    'nueva_contraseña' => 'required|min:5',
    'nueva_contraseña_confirmation' => 'required|min:5|same:nueva_contraseña',
], [
    'nueva_contraseña.min' => 'La nueva contraseña debe tener al menos 5 caracteres.',
    'nueva_contraseña_confirmation.min' => 'La confirmación no coincide con la nueva contraseña.',
]);

if ($request->nueva_contraseña == $request->password_actual) {
    return back()->withErrors(['nueva_contraseña_confirmation' => 'La nueva contraseña es igual a la anterior']);
}

if ($request->nueva_contraseña !== $request->nueva_contraseña_confirmation) {
    return back()->withErrors(['nueva_contraseña_confirmation' => 'La contraseña a confirmar es distinta de la nueva']);
}
    
        $usuarioSession = Auth::guard('users')->user();
        $usuario = Usuario::where('dni', $usuarioSession->dni)->first();
    
        if (!$usuario) {
            return back()->withErrors(['mensaje' => 'Usuario no encontrado']);
        }
    
        if (!Hash::check($request->password_actual, $usuario->contraseña)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta']);
        }
    
        $usuario->contraseña = bcrypt($request->nueva_contraseña);
        $usuario->save();
    
        return back()->with('success', 'Contraseña actualizada correctamente');
    }

    public function eliminarCuentaPropia()
{
    $usuario = Auth::guard('users')->user();
    $rol=$usuario->rol;
    if ($rol=='cliente' && Reserva::where('id_cliente', $usuario->id_usuario)
            ->whereIn('estado', ['pendiente', 'activa'])
            ->exists()){
        return back()->with('error', 'No se puede eliminar una cuenta con reservas confirmadas o pendientes');
    }
    Auth::guard('users')->logout(); // cierra sesión
    Usuario::where('dni', $usuario->dni)->delete();
    session()->forget('layout');
    return redirect('/')->with('success', 'Tu cuenta fue eliminada correctamente.');
}




}
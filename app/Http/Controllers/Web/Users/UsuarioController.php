<?php

namespace App\Http\Controllers\Web\Users;

use App\Domain\Reserva\Models\Reserva;
use App\Domain\User\Models\Usuario;
use App\Enums\Estados;
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

   public function updatePassword(Request $request)
{
    $request->validate([
        'password_actual' => ['required'],
        'nueva_contraseña' => ['required', 'min:6', 'different:password_actual'],
        'nueva_contraseña_confirmation' => ['required', 'same:nueva_contraseña'],
    ], [
        'nueva_contraseña.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
        'nueva_contraseña.different' => 'La nueva contraseña debe ser distinta a la actual.',
        'nueva_contraseña_confirmation.same' => 'La confirmación no coincide con la nueva contraseña.',
    ]);

    $usuarioSession = Auth::guard('users')->user();
    $usuario = Usuario::where('dni', $usuarioSession->dni)->first();

    if (!$usuario) {
        return back()->with('error', 'Usuario no encontrado.');
    }

    if (!Hash::check($request->password_actual, $usuario->contraseña)) {
        return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
    }

    $usuario->contraseña = bcrypt($request->nueva_contraseña);
    $usuario->save();

    return back()->with('success', 'Contraseña actualizada correctamente.');
}

    public function logicDelete($dni){
        $usuario = Usuario::where('dni', $dni)->first();
        $usuario->update([
            'estado' => Estados::INACTIVO,
        ]);
    }


    public function eliminarCuentaPropia()
{
    $usuario = Auth::guard('users')->user();
    $rol=$usuario->rol;
    if ($rol=='cliente' && Reserva::where('id_cliente', $usuario->id_usuario)
            ->whereIn('estado', ['aprobada', 'activa'])
            ->exists()){
        return back()->with('error', 'No se puede eliminar una cuenta con reservas activas');
    }
    Auth::guard('users')->logout(); // cierra sesión
    $this->logicDelete($usuario->dni); //Borrado logico
    session()->forget('layout');
    return redirect('/')->with('success', 'Tu cuenta fue eliminada correctamente.');
}

}
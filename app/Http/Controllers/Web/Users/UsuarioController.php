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
            'contraseña' => 'required|string|min:6',
            'rol' => 'required|string',
            'dni' => 'required|string|unique:usuarios,dni',
            'telefono' => 'required|string',
            'estado' => 'required|string',
            'fecha_nacimiento' => 'required','before_or_equal:' . now()->subYears(18)->format('Y-m-d'), 
            'fecha_alta' => 'required|date',
        ],[
        
            'nombre.required' => 'El nombre es obligatorio.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',

            'contraseña.required' => 'La contraseña es obligatoria.',
            'contraseña.string' => 'La contraseña debe ser una cadena de texto.',
            'contraseña.min' => 'La contraseña debe tener al menos 6 caracteres.',

            'rol.required' => 'El rol es obligatorio.',
            'rol.string' => 'El rol debe ser una cadena de texto.',

            'dni.required' => 'El DNI es obligatorio.',
            'dni.string' => 'El DNI debe ser una cadena de texto.',
            'dni.unique' => 'Este DNI ya está registrado.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.string' => 'El teléfono debe ser una cadena de texto.',

            'estado.required' => 'El estado es obligatorio.',
            'estado.string' => 'El estado debe ser una cadena de texto.',

            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => '',

            'fecha_alta.required' => 'La fecha de alta es obligatoria.',
            'fecha_alta.date' => 'La fecha de alta debe ser una fecha válida.',
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
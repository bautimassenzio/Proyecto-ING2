<?php

namespace App\Http\Controllers\Web\Users;


use App\Domain\User\Models\Usuario;
use App\Enums\Estados;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\Roles;


class UsuarioController extends Controller
{

    //Obtener todos los usuarios
    public function getUsuarios(){ 
        return Usuario::all();
    }

    // Obtener usuario por DNI (GET)
    public function getUsuario($dni){ 
        $usuario = Usuario::where('dni', $dni)->first(); //Busqueda por DNI
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

    // Eliminar fisica de usuario por DNI (DELETE)
    public function delete($dni)
    {
        $usuario = Usuario::where('dni', $dni)->first();

        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['mensaje' => 'Usuario eliminado con éxito']);
    }

   // Actualizar contraseña de un usuario 
   public function updatePassword(Request $request) { 
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

        $usuario->contraseña = bcrypt($request->nueva_contraseña); // Se encripta la contraseña
        $usuario->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }   

    //Eliminacion logica de un usuario o empleado en la DB
    public static function logicDelete($email) { 
        $usuario = Usuario::where('email', $email)->first();
        $usuario->update([
            'estado' => Estados::INACTIVO, //Paso el estado del usuario a inactivo
        ]);
    }


public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string',
        'email' => 'required|email|unique:usuarios,email',
        'contraseña' => 'required|string|min:4',
        'dni' => 'required|string|unique:usuarios,dni|regex:/^\d{7,8}$/',
        'telefono' => 'required|string',
        'fecha_nacimiento' => ['required', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],
        'rol' => 'required|in:' . Roles::ADMINISTRADOR->value . ',' . Roles::EMPLEADO->value,
    ],[
        'nombre.required' => 'El nombre es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Debe ser una dirección válida.',
        'email.unique' => 'Este correo ya está registrado.',
        'contraseña.required' => 'La contraseña es obligatoria.',
        'contraseña.min' => 'Debe tener al menos 4 caracteres.',
        'dni.required' => 'El DNI es obligatorio.',
        'dni.unique' => 'Este DNI ya está registrado.',
        'dni.regex' => 'Debe tener 7 u 8 dígitos numéricos.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
        'fecha_nacimiento.before' => 'Debe ser mayor de 18 años.',
        'rol.required' => 'El rol es obligatorio.',
        'rol.in' => 'Rol inválido. Debe ser ADMIN o EMPLEADO.'
    ]);

    $usuario = Usuario::create([
        'nombre' => $request->nombre,
        'email' => $request->email,
        'contraseña' => bcrypt($request->contraseña),
        'rol' => $request->rol,
        'dni' => $request->dni,
        'telefono' => $request->telefono,
        'estado' => Estados::ACTIVO,
        'fecha_nacimiento' => $request->fecha_nacimiento,
        'fecha_alta' => now(),
    ]);

    return redirect()->back()->with('success', 'Usuario creado exitosamente.');
}


}
<?php

namespace App\Http\Controllers\Web\Users;

use App\Domain\User\Models\Usuario;
use App\Enums\Estados;
use App\Enums\Roles; // De ambas ramas
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // De ambas ramas
use App\Domain\Reserva\Models\Reserva; // Solo de la rama entrante (necesario para delete)

class UsuarioController extends Controller
{
    // Obtener todos los usuarios (idéntico en ambas ramas)
    public function getUsuarios(){
        return Usuario::all();
    }

    // Obtener todos los empleados con paginación (nuevo de la rama entrante)
    public function getEmpleados(){
        $usuarios = Usuario::where('rol', Roles::EMPLEADO)->paginate(10); // 10 por página
        return view('eliminarUsuario', compact('usuarios')); // Asumo que esta vista es la correcta
    }

    // Obtener todos los clientes con paginación (nuevo de la rama entrante)
    public function getClientes() {
        $usuarios = Usuario::where('rol', Roles::CLIENTE)->paginate(10); // 10 por página
        return view('eliminarUsuario', compact('usuarios')); // Asumo que esta vista es la correcta
    }

    // Obtener usuario por DNI (GET) (idéntico en ambas ramas, salvo el return en incoming)
    public function getUsuario($dni){
        $usuario = Usuario::where('dni', $dni)->first(); //Busqueda por DNI
        if (!$usuario) {
            // Se mantiene el return con JSON para API consistency, si se usa para vistas, adaptar.
            // La rama entrante retornaba $usuario directamente sin response()->json
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario); // Mantener JSON para consistencia API
    }

    // Actualizar usuario por DNI (PUT) (idéntico en ambas ramas, se actualiza bcrypt a Hash::make)
    public function update(Request $request, $email)
    {
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'string',
            'email' => 'email|unique:usuarios,email,' . $usuario->id_usuario, // Usar id_usuario si ese es el nombre de la PK
            'contraseña' => 'string|min:4',
            'rol' => 'string',
            'telefono' => 'string',
            'estado' => 'string',
            'fecha_alta' => 'date',
        ]);

        $usuario->update([
            'nombre' => $request->nombre ?? $usuario->nombre,
            'email' => $request->email ?? $usuario->email,
            'contraseña' => $request->contraseña ? Hash::make($request->contraseña) : $usuario->contraseña, // Usar Hash::make
            'rol' => $request->rol ?? $usuario->rol,
            'telefono' => $request->telefono ?? $usuario->telefono,
            'estado' => $request->estado ?? $usuario->estado,
            'fecha_alta' => $request->fecha_alta ?? $usuario->fecha_alta,
        ]);

        return response()->json(['mensaje' => 'Usuario actualizado con éxito', 'usuario' => $usuario]);
    }

    // Eliminar Lógica de usuario por DNI (DELETE) - Priorizamos la lógica de la rama entrante
    public function delete($dni)
    {
        $usuario = Usuario::where('dni', $dni)->first(); // Obtenemos el usuario directamente

        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuario no encontrado.'); // Mantener el redirect para consistencia con la lógica del incoming delete
        }

        $rol = $usuario->rol;
        // Si es cliente, verificar que no tenga reservas activas
        if ($rol === Roles::CLIENTE && Reserva::where('id_cliente', $usuario->id_usuario)
                                          ->whereIn('estado', ['aprobada', 'activa'])
                                          ->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar una cuenta con reservas activas.');
        }

        // Realizar borrado lógico
        $this->logicDelete($dni); // Llama al método estático de la misma clase

        session()->forget('layout'); // Mantener si es necesario para tu flujo de aplicación
        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }

    // Actualizar contraseña de un usuario (idéntico en ambas ramas, se actualiza bcrypt a Hash::make)
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

        $usuario->contraseña = Hash::make($request->nueva_contraseña); // Usar Hash::make
        $usuario->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    // Eliminación lógica de un usuario o empleado en la DB (Priorizamos la versión con DNI)
    public function logicDelete($dni) { // Cambié a public function si es llamada por $this
        $usuario = Usuario::where('dni', $dni)->first();
        if ($usuario) { // Añadir verificación por si el usuario no se encuentra (aunque ya se hizo en delete)
            $usuario->update([
                'estado' => Estados::INACTIVO, // Paso el estado del usuario a inactivo
            ]);
        }
    }

    // Reactiva la cuenta de un usuario (nuevo de la rama entrante)
    public function activateUser($dni) {
        $usuario = Usuario::where('dni', $dni)->first();
        if ($usuario) { // Añadir verificación por si el usuario no se encuentra
            $usuario->update([
                'estado' => Estados::ACTIVO,
            ]);
            return redirect()->back()->with('success', 'Usuario dado de alta correctamente.');
        }
        return redirect()->back()->with('error', 'Usuario no encontrado para activar.');
    }

    // Almacena un nuevo usuario (ADMIN o EMPLEADO) (original de tu rama)
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
            'contraseña' => Hash::make($request->contraseña), // Usar Hash::make
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
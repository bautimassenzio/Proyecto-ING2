<?php

namespace App\Http\Controllers\Web\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\Maquinaria\Models\Maquinaria;
use App\Domain\User\Models\Usuario;
use App\Enums\Estados;
use App\Enums\Roles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\EnviarContraseña;
use Illuminate\Support\Facades\Mail;

class EmpleadoController extends Controller
{
    // --- MÉTODOS DE TU RAMA ---

    // Los métodos registrarEntrega y registrarDevolucion han sido movidos a ReservaController.php
    // para centralizar la lógica de negocio de las reservas.

    // 3. Consultar historial de reservas de un cliente
    public function mostrarFormularioHistorial()
    {
        $clientes = Usuario::where('rol', 'cliente')->get();
        $layout = session('layout', 'layouts.base');

        return view('empleado.formulario-historial', compact('clientes', 'layout'));
    }

    public function historialReservasCliente($dni)
    {
        $cliente = Usuario::where('dni', $dni)->first();

        if (!$cliente || $cliente->rol !== 'cliente') {
            return redirect()->route('empleado.resultado-historial')->with('error', 'Cliente no encontrado.');
        }

        $reservas = Reserva::with('maquinaria')
            ->where('id_cliente', $cliente->id_usuario)
            ->orderByDesc('fecha_inicio')
            ->get();

        $layout = session('layout', 'layouts.base');

        return view('empleado.resultado-historial', compact('cliente', 'reservas', 'layout'));
    }

    // 4. Registrar un cliente nuevo (renombrado de tu rama para evitar conflicto con la lógica de empleado)
    public function registrarClienteDesdeEmpleado(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'dni' => 'required|numeric|unique:usuarios,dni',
            'email' => 'required|email|unique:usuarios,email',
            'contraseña' => 'required|string|min:4',
            'telefono' => 'nullable|string',
        ]);

        $cliente = Usuario::create([
            'nombre' => $request->nombre,
            'dni' => $request->dni,
            'email' => $request->email,
            'contraseña' => Hash::make($request->contraseña),
            'telefono' => $request->telefono,
            'rol' => 'cliente',
            'estado' => Estados::ACTIVO,
            'fecha_alta' => now(),
        ]);

        return response()->json(['mensaje' => 'Cliente registrado con éxito', 'cliente' => $cliente]);
    }

    // --- MÉTODOS DE LA RAMA ENTRANTE (creación de empleados) ---

    // Almacena un empleado desde la vista del admin
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

    // Almacena un usuario con rol empleado (originalmente crearEmpleado en incoming)
    public function crearEmpleado(Request $request){
        $rol = Roles::EMPLEADO;
        $estado = Estados::ACTIVO;
        return Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'contraseña' => Hash::make($request->contraseña),
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
        Mail::to($request->email)->send(new EnviarContraseña($request->nombre, $contraseñaGenerada));
        return $response;
    }
}

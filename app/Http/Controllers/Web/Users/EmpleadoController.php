<?php

namespace App\Http\Controllers\Web\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Reserva\Models\Reserva; // De tu rama
use App\Domain\Maquinaria\Models\Maquinaria; // De tu rama
use App\Domain\User\Models\Usuario;
use App\Enums\Estados; // De tu rama (y en incoming)
use App\Enums\Roles; // De incoming
use Illuminate\Support\Facades\Hash; // De tu rama (importante para bcrypt si se usa en otro lado)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // De tu rama
use Illuminate\Support\Str; // De incoming
use App\Mail\EnviarContraseña; // De incoming
use Illuminate\Support\Facades\Mail; // De incoming

class EmpleadoController extends Controller
{
    // --- MÉTODOS DE TU RAMA ---

    // 1. Registrar entrega de maquinaria
    public function registrarEntrega($reserva_id)
    {
        $reserva = Reserva::find($reserva_id);

        if (!$reserva) {
            return response()->json(['mensaje' => 'Reserva no encontrada'], 404);
        }

        $reserva->update([
            'fecha_entrega' => now(),
            'empleado_entrega_id' => Auth::id(), // supondremos que el empleado está logueado
        ]);

        return response()->json(['mensaje' => 'Entrega registrada con éxito', 'reserva' => $reserva]);
    }

    // 2. Registrar devolución de maquinaria
    public function registrarDevolucion($reserva_id)
    {
        $reserva = Reserva::find($reserva_id);

        if (!$reserva) {
            return response()->json(['mensaje' => 'Reserva no encontrada'], 404);
        }

        $reserva->update([
            'fecha_devolucion' => now(),
            'empleado_recepcion_id' => Auth::id(), // suponiendo que también se guarda quien recibe
        ]);

        return response()->json(['mensaje' => 'Devolución registrada con éxito', 'reserva' => $reserva]);
    }

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
            'contraseña' => Hash::make($request->contraseña), // Usar Hash::make en lugar de bcrypt directamente
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
    public function crearEmpleado(Request $request){ // Añadí Request $request para que sea más claro. Si solo se usa internamente, podrías quitarlo si la rama original lo pasaba de otra forma.
        $rol = Roles::EMPLEADO;
        $estado = Estados::ACTIVO;
        return Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'contraseña' => Hash::make($request->contraseña), // Usar Hash::make
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
        Mail::to($request->email)->send(new EnviarContraseña($request->nombre, $contraseñaGenerada)); //Envia un mail al nuevo usuario con su contraseña
        return $response;
    }
}
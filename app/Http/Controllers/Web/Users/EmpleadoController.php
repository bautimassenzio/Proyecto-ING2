<?php

namespace App\Http\Controllers\Web\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\Maquinaria\Models\Maquinaria;
use App\Domain\User\Models\Usuario;
use App\Enums\Estados;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmpleadoController extends Controller
{
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

    // 4. Registrar un cliente nuevo
    public function registrarCliente(Request $request)
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
            'contraseña' => bcrypt($request->contraseña),
            'telefono' => $request->telefono,
            'rol' => 'cliente',
            'estado' => Estados::ACTIVO,
            'fecha_alta' => now(),
        ]);

        return response()->json(['mensaje' => 'Cliente registrado con éxito', 'cliente' => $cliente]);
    }
}

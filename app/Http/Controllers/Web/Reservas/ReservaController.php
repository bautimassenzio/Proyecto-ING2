<?php

namespace App\Http\Controllers\Web\Reservas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\User\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // ¡Importa la fachada Auth!


class ReservaController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva reserva.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // No necesitamos listar clientes si el ID viene de la sesión.
        // Opcionalmente, podrías querer pasar el usuario autenticado a la vista
        // para mostrar su nombre o email, pero no es estrictamente necesario
        // para el ID.
        $clienteAutenticado = Auth::user();

        return view('reservas.create', compact('clienteAutenticado'));
    }

    /**
     * Almacena una nueva reserva en la base de datos.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Obtener el ID del cliente autenticado
        $idClienteAutenticado = Auth::guard('users')->id();

        // Si no hay un usuario autenticado, redirigir con un error.
        // Esto debería ser manejado por el middleware de autenticación,
        // pero es una buena práctica de seguridad.
        if (!$idClienteAutenticado) {
            return back()->withErrors(['usuario' => 'Debes iniciar sesión para realizar una reserva.'])->withInput();
        }

        // 1. Validación de los campos del formulario
        // Eliminamos 'id_cliente' de la validación ya que no viene del formulario
        $request->validate([
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ], [
            'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ]);

        // 2. Validación de duración de la reserva (entre 2 y 30 días)
        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $duracion = $fechaInicio->diffInDays($fechaFin);

        if ($duracion < 2 || $duracion > 30) {
            return back()->withErrors(['duracion' => 'La reserva debe ser entre 2 y 30 días.'])->withInput();
        }

        // 3. Verificar si el usuario autenticado ya tiene una reserva pendiente o activa
        $usuarioTieneReservaActiva = Reserva::where('id_cliente', $idClienteAutenticado)
            ->whereIn('estado', ['pendiente', 'activa'])
            ->exists();

        if ($usuarioTieneReservaActiva) {
            return back()->withErrors(['usuario' => 'Ya tienes una reserva pendiente o activa.'])->withInput();
        }

        // 4. Calcular el pago total (valor de prueba por ahora)
        $valorPorDia = 50; // Valor de prueba por día
        $pagoTotal = $valorPorDia * $duracion; // Cálculo simple de prueba

        try {
            // 5. Crear la reserva en la base de datos usando el ID del usuario autenticado
            $reserva = Reserva::create([
                'id_cliente' => $idClienteAutenticado, // Usamos el ID del usuario autenticado
                'id_maquinaria' => 1, // Mantener este valor o hacerlo dinámico si aplica
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'fecha_reserva' => Carbon::now(),
                'estado' => 'pendiente',
                'total' => $pagoTotal,
                'id_empleado' => null,
            ]);

            // Guardar el ID de la reserva en la sesión
            session(['reserva_id' => $reserva->id_reserva]);

            // Redirigir a la página de pago
            return redirect()->route('pago.seleccionar')->with('success', 'Reserva creada con éxito. Proceda al pago.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Hubo un error al crear la reserva: ' . $e->getMessage()]);
        }
    }
}
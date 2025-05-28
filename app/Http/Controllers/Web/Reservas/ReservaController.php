<?php

namespace App\Http\Controllers\Web\Reservas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\User\Models\Usuario; // Para obtener clientes
use Carbon\Carbon; // Para trabajar con fechas


class ReservaController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva reserva.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // En este formulario, solo necesitamos listar los usuarios que pueden ser clientes.
        // Asumiendo que tienes un 'rol' en tu tabla de usuarios, podrías filtrarlos así:
        // $clientes = Usuario::where('rol', 'cliente')->get();
        // Por ahora, obtenemos todos para simplificar la prueba.
        $clientes = Usuario::all();

        return view('reservas.create', compact('clientes'));
    }

    /**
     * Almacena una nueva reserva en la base de datos.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validación de los campos del formulario
        $request->validate([
            'id_cliente' => 'required|exists:usuarios,id_usuario', // Cambiado a id_cliente
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

        // 3. Verificar si el usuario ya tiene una reserva pendiente o activa
        $usuarioTieneReservaActiva = Reserva::where('id_cliente', $request->id_cliente) // Cambiado a id_cliente
            ->whereIn('estado', ['pendiente', 'activa']) // Cambiado a 'estado'
            ->exists();

        if ($usuarioTieneReservaActiva) {
            return back()->withErrors(['usuario' => 'El cliente ya tiene una reserva pendiente o activa.'])->withInput();
        }

        // 4. Calcular el pago total (valor de prueba por ahora)
        $valorPorDia = 50; // Valor de prueba por día
        $pagoTotal = $valorPorDia * $duracion; // Cálculo simple de prueba

       try {
        // 5. Crear la reserva en la base de datos
        $reserva = Reserva::create([
            'id_cliente' => $request->id_cliente,
            'id_maquinaria'=> 1,
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
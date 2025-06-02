<?php

namespace App\Http\Controllers\Web\Reservas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\Maquinaria\Models\Maquinaria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\ReservaCancelada;
use Illuminate\Support\Facades\Mail;

class ReservaController extends Controller
{
    public function create(Request $request)
    {
        $clienteAutenticado = Auth::user();
        $idMaquinaria = $request->query('id_maquinaria');

        $maquinaria = Maquinaria::findOrFail($idMaquinaria);

        $fechasOcupadas = Reserva::where('id_maquinaria', $idMaquinaria)
            ->whereIn('estado', ['pendiente', 'activa'])
            ->get(['fecha_inicio', 'fecha_fin']);

        return view('reservas.create', compact('clienteAutenticado', 'maquinaria', 'fechasOcupadas'));
    }

    public function store(Request $request)
    {
        $idClienteAutenticado = Auth::id();

        if (!$idClienteAutenticado) {
            return back()->withErrors(['usuario' => 'Debes iniciar sesión para realizar una reserva.'])->withInput();
        }

        $request->validate([
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'id_maquinaria' => 'required|exists:maquinarias,id_maquinaria',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $duracion = $fechaInicio->diffInDays($fechaFin);

        if ($duracion < 2 || $duracion > 30) {
            return back()->withErrors(['duracion' => 'La reserva debe ser entre 2 y 30 días.'])->withInput();
        }

        // --- VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA EL CLIENTE ---
        $clienteTieneReservaSolapada = Reserva::where('id_cliente', $idClienteAutenticado)
            ->whereIn('estado', ['pendiente', 'activa'])
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
                // Un rango [A, B] se solapa con [C, D] si A <= D AND C <= B
                // En nuestro caso:
                // A = fechaInicio (de la nueva reserva)
                // B = fechaFin (de la nueva reserva)
                // C = fecha_inicio (de la reserva existente en DB)
                // D = fecha_fin (de la reserva existente en DB)
                $query->where('fecha_inicio', '<=', $fechaFin) // El inicio de la existente es antes o igual al fin de la nueva
                      ->where('fecha_fin', '>=', $fechaInicio); // El fin de la existente es después o igual al inicio de la nueva
            })
            ->exists();

        if ($clienteTieneReservaSolapada) {
            return back()->withErrors(['usuario' => 'Ya tienes una reserva activa o pendiente en ese rango de fechas.'])->withInput();
        }
        // --- FIN VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA EL CLIENTE ---


        // --- VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA LA MAQUINARIA ---
        $maquinariaReservada = Reserva::where('id_maquinaria', $request->id_maquinaria)
            ->whereIn('estado', ['pendiente', 'activa'])
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
                // La misma lógica de solapamiento para la maquinaria
                $query->where('fecha_inicio', '<=', $fechaFin)
                      ->where('fecha_fin', '>=', $fechaInicio);
            })
            ->exists();

        if ($maquinariaReservada) {
            return back()->withErrors(['fecha_inicio' => 'Esta maquinaria no está disponible en el período seleccionado.'])->withInput();
        }
        // --- FIN VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA LA MAQUINARIA ---


        $maquinaria = Maquinaria::find($request->id_maquinaria);
        if (!$maquinaria) {
            return back()->withErrors(['maquinaria' => 'No se encontró la maquinaria seleccionada.'])->withInput();
        }

        $pagoTotal = $maquinaria->precio_dia * $duracion;

        try {
            $reserva = Reserva::create([
                'id_cliente' => $idClienteAutenticado,
                'id_maquinaria' => $request->id_maquinaria,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'fecha_reserva' => Carbon::now(),
                'estado' => 'pendiente',
                'total' => $pagoTotal,
                'id_empleado' => null,
            ]);

            session(['reserva_id' => $reserva->id_reserva]);

            return redirect()->route('pago.seleccionar')->with('success', 'Reserva creada con éxito. Proceda al pago.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error al crear la reserva: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $cliente = Auth::user();

        if (!$cliente) {
            return redirect()->route('login')->withErrors(['auth' => 'Debes iniciar sesión para ver tus reservas.']);
        }

        $reservas = Reserva::with('maquinaria')
            ->where('id_cliente', $cliente->id)
            ->orderBy('fecha_reserva', 'desc')
            ->get();

        return view('reservas.historial', compact('reservas'));
    }

    public function cancelar(Request $request, $id_reserva)
    {
        $cliente = Auth::user();

        if (!$cliente) {
            return redirect()->route('login')->withErrors(['auth' => 'Debes iniciar sesión para cancelar una reserva.']);
        }

        $reserva = Reserva::with('maquinaria')->where('id_reserva', $id_reserva)->where('id_cliente', $cliente->id)->first();

        if (!$reserva) {
            return back()->withErrors(['reserva' => 'Reserva no encontrada.']);
        }

        $ahora = \Carbon\Carbon::now();
        $limiteCancelacion = $reserva->fecha_inicio->copy()->subDay();

        if ($ahora->gt($limiteCancelacion)) {
            return back()->withErrors(['cancelacion' => 'La reserva sólo puede cancelarse con al menos 24 horas de anticipación.']);
        }

        $reserva->estado = 'cancelada';
        $reserva->save();

        $politicaCancelacion = $reserva->maquinaria->politica_cancelacion;
        Mail::to($cliente->email)->send(new ReservaCancelada($reserva, $politicaCancelacion));

        return back()->with('success', 'Reserva cancelada con éxito. Se ha enviado un correo con la política de cancelación.');
    }
}
<?php

namespace App\Http\Controllers\Web\Reservas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\Maquinaria\Maquinaria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Mail\ReservaCancelada;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; 

class ReservaController extends Controller
{
    public function create(Request $request)
    {
        
        $clienteAutenticado = Auth::user();
        $idMaquinaria = $request->query('id_maquinaria');

        $maquinaria = Maquinaria::findOrFail($idMaquinaria);
        
        $fechasOcupadas = Reserva::where('id_maquinaria', $idMaquinaria)
            ->whereIn('estado', ['pendiente', 'aprobada'])
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
            ->whereIn('estado', ['pendiente', 'aprobada'])
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
            return back()->withErrors(['usuario' => 'Ya tienes una reserva aprobada o pendiente en ese rango de fechas.'])->withInput();
        }
        // --- FIN VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA EL CLIENTE ---


        // --- VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA LA MAQUINARIA ---
        $maquinariaReservada = Reserva::where('id_maquinaria', $request->id_maquinaria)
            ->whereIn('estado', ['pendiente', 'aprobada'])
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
            ->where('id_cliente', $cliente->id_usuario)
            ->orderBy('fecha_reserva', 'desc')
            ->get();

        $layout = session('layout', 'layouts.base');
        return view('reservas.historial', compact('reservas', 'layout'));
    }

    public function cancelar(Request $request, $id_reserva)
    {
        $cliente = Auth::user();

        if (!$cliente) {
            return redirect()->route('login')->withErrors(['auth' => 'Debes iniciar sesión para cancelar una reserva.']);
        }

        $reserva = Reserva::with('maquinaria')->where('id_reserva', $id_reserva)->where('id_cliente', $cliente->id_usuario)->first();

        if (!$reserva) {
            return back()->withErrors(['reserva' => 'Reserva no encontrada.']);
        }

        $ahora = \Carbon\Carbon::now();
        $limiteCancelacion = \Carbon\Carbon::parse($reserva->fecha_inicio)->subDay();
      

        if ($ahora->gt($limiteCancelacion)) {
            return back()->withErrors(['cancelacion' => 'La reserva sólo puede cancelarse con al menos 24 horas de anticipación.']);
        }

        $reserva->estado = 'cancelada';
        $reserva->save();

        $politicaCancelacion = $reserva->maquinaria->politica->tipo;
        Mail::to($cliente->email)->send(new ReservaCancelada($reserva, $politicaCancelacion));

        return back()->with('success', 'Reserva cancelada con éxito. Se ha enviado un correo con la política de cancelación.');
    }

    public function registrarDevolucion(Reserva $reserva)
    {
        Log::info('Intentando registrar devolución para Reserva ID: ' . $reserva->id_reserva);
        $today = Carbon::today();
        $fechaInicioReserva = Carbon::parse($reserva->fecha_inicio);

        try {
            // 1. Verificar si la reserva ya está finalizada o cancelada
            if ($reserva->estado === 'finalizada' || $reserva->estado === 'cancelada') {
                Log::warning('Intento de registrar devolución para reserva ya finalizada/cancelada. ID: ' . $reserva->id_reserva);
                return redirect()->route('maquinarias.devoluciones-pendientes')->with('error', 'La reserva ya ha sido finalizada o cancelada.');
            }

            if ($today->lt($fechaInicioReserva)) { // Si hoy es ANTES de la fecha de inicio
                Log::warning('Intento de devolver reserva cuya fecha de inicio aún no ha llegado. ID: ' . $reserva->id_reserva);
                return redirect()->route('maquinarias.devoluciones-pendientes')->with('error', 'No se puede registrar la devolución de una maquinaria cuya reserva aún no ha comenzado.');
            }

            // 2. Actualizar el estado de la reserva a 'finalizada'
            $reserva->estado = 'finalizada';
            // Asigna el ID del empleado que registra la devolución
            // Asegúrate de que 'id_empleado' sea fillable en tu modelo Reserva
            $reserva->id_empleado = Auth::id();
            $reserva->save();
            Log::info('Reserva ID ' . $reserva->id_reserva . ' actualizada a estado "finalizada".');

            // 3. Actualizar el estado de la maquinaria a 'disponible'
            // Asegúrate de que la relación 'maquinaria' esté definida en el modelo Reserva
            // y que 'estado' sea fillable en el modelo Maquinaria
            if ($reserva->maquinaria) {
                $reserva->maquinaria->estado = 'disponible';
                $reserva->maquinaria->save();
                Log::info('Maquinaria ID ' . $reserva->maquinaria->id_maquinaria . ' actualizada a estado "disponible".');
            } else {
                Log::error('No se pudo encontrar la maquinaria asociada para la Reserva ID: ' . $reserva->id_reserva);
                return redirect()->route('maquinarias.devoluciones-pendientes')->with('error', 'No se pudo encontrar la maquinaria asociada a la reserva.');
            }

            return redirect()->route('maquinarias.devoluciones-pendientes')->with('success', 'Devolución registrada exitosamente. Reserva finalizada y maquinaria disponible.');

        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar devolución para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('maquinarias.devoluciones-pendientes')->with('error', 'Error al registrar la devolución en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error inesperado al registrar devolución para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('maquinarias.devoluciones-pendientes')->with('error', 'Ocurrió un error inesperado al registrar la devolución: ' . $e->getMessage());
        }
    }

    public function listasParaEntregar()
    {
        Log::info('Accediendo a listasParaEntregar.');
        $reservasListasParaEntregar = Reserva::with(['maquinaria', 'cliente']) // Carga la maquinaria y el usuario relacionados
            ->where('estado', 'aprobada') // Solo reservas aprobadas
            //->whereDate('fecha_inicio', '<=', Carbon::today()) // La fecha de inicio es hoy o ya pasó
            ->whereNotIn('estado', ['finalizada', 'cancelada']) // Excluir reservas ya finalizadas o canceladas
            ->orderBy('fecha_inicio', 'asc') // Ordena por la fecha de inicio más antigua primero
            ->get();

        Log::info('Reservas listas para entregar encontradas: ' . $reservasListasParaEntregar->count());

        return view('reservas.listas-para-entregar', compact('reservasListasParaEntregar'));
    }

    public function registrarEntrega(Reserva $reserva)
    {
        Log::info('Intentando registrar entrega para Reserva ID: ' . $reserva->id_reserva);
        $today = Carbon::today();

        try {
            // 1. Validar estado de la reserva
            if ($reserva->estado !== 'aprobada') {
                Log::warning('Intento de registrar entrega para reserva en estado incorrecto. ID: ' . $reserva->id_reserva . ', Estado: ' . $reserva->estado);
                return redirect()->route('reservas.listas-para-entregar')->with('error', 'La reserva no está en estado "aprobada" para ser entregada.');
            }

            // 2. Validar fechas de entrega
            $fechaInicio = Carbon::parse($reserva->fecha_inicio);
            $fechaFin = Carbon::parse($reserva->fecha_fin);

            if ($today->lt($fechaInicio) || $today->gt($fechaFin)) {
                Log::warning('Intento de registrar entrega fuera del rango de fechas. ID: ' . $reserva->id_reserva . ', Hoy: ' . $today->format('Y-m-d') . ', Inicio: ' . $fechaInicio->format('Y-m-d') . ', Fin: ' . $fechaFin->format('Y-m-d'));
                return redirect()->route('reservas.listas-para-entregar')->with('error', 'La entrega solo puede registrarse en o después de la fecha de inicio y en o antes de la fecha de fin de la reserva.');
            }

            // Asigna el ID del empleado que registra la entrega
            $reserva->id_empleado = Auth::id();
            $reserva->save(); // Guardamos solo la asignación del empleado
            Log::info('Reserva ID ' . $reserva->id_reserva . ' registrada con id_empleado: ' . $reserva->id_empleado);

            // Redirigir a la lista de devoluciones pendientes
            return redirect()->route('maquinarias.devoluciones-pendientes')->with('success', 'Entrega registrada exitosamente.');

        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar entrega para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('reservas.listas-para-entregar')->with('error', 'Error al registrar la entrega en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error inesperado al registrar entrega para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('reservas.listas-para-entregar')->with('error', 'Ocurrió un error inesperado al registrar la entrega: ' . $e->getMessage());
        }
    }
}
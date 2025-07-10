<?php

namespace App\Http\Controllers\Web\Reservas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
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

        $fechasMaquinaria = Reserva::where('id_maquinaria', $idMaquinaria)
        ->whereIn('estado', ['pendiente', 'aprobada', 'en_curso']) // Considerar también 'en_curso'
        ->get(['fecha_inicio', 'fecha_fin']);

        $fechasCliente = Reserva::where('id_cliente', $clienteAutenticado->id_usuario)
        ->whereIn('estado', ['pendiente', 'aprobada', 'en_curso']) // Considerar también 'en_curso'
        ->get(['fecha_inicio', 'fecha_fin']);

       $fechasOcupadas = $fechasMaquinaria->concat($fechasCliente)->map(function($reserva) {
        return [
            'fecha_inicio' => \Carbon\Carbon::parse($reserva->fecha_inicio)->toDateString(),
            'fecha_fin' => \Carbon\Carbon::parse($reserva->fecha_fin)->toDateString(),
        ];
    })->values();
        $layout = session('layout', 'layouts.cliente');
        return view('reservas.create', compact('clienteAutenticado', 'maquinaria', 'fechasOcupadas', 'layout'));
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
        ], [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio no es válida.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o una fecha posterior.',
            
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin no es válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',

            'id_maquinaria.required' => 'Debe seleccionar una maquinaria.',
            'id_maquinaria.exists' => 'La maquinaria seleccionada no es válida.',
        ]);


        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $duracion = $fechaInicio->diffInDays($fechaFin);

        if ($duracion < 2 || $duracion > 30) {
            return back()->withErrors(['duracion' => 'La reserva debe ser entre 2 y 30 días.'])->withInput();
        }

        // --- VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA EL CLIENTE ---
        $clienteTieneReservaSolapada = Reserva::where('id_cliente', $idClienteAutenticado)
            ->whereIn('estado', ['pendiente', 'aprobada', 'en_curso']) // Considerar 'en_curso'
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
                $query->where('fecha_inicio', '<=', $fechaFin)
                      ->where('fecha_fin', '>=', $fechaInicio);
            })
            ->exists();

        if ($clienteTieneReservaSolapada) {
            return back()->withErrors(['usuario' => 'Ya tienes una reserva aprobada, pendiente o en curso en ese rango de fechas.'])->withInput();
        }
        // --- FIN VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA EL CLIENTE ---


        // --- VERIFICACIÓN DE SOLAPAMIENTO DE RESERVAS PARA LA MAQUINARIA ---
        $maquinariaReservada = Reserva::where('id_maquinaria', $request->id_maquinaria)
            ->whereIn('estado', ['pendiente', 'aprobada', 'en_curso']) // Considerar 'en_curso'
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
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
                'estado' => 'pendiente', // La reserva se crea como pendiente, no cancelada
                'total' => $pagoTotal,
                'id_empleado' => null,
            ]);

            session(['reserva_id' => $reserva->id_reserva]);
            
            return redirect()->route('pago.seleccionar')->with('success', 'Reserva creada con éxito. Proceda al pago.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error al crear la reserva: ' . $e->getMessage()]);
        }
    }


    // Mostrar historial de reservas
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

    // Cancelar Reserva
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

        if ($reserva->estado === 'cancelada' || $reserva->estado === 'finalizada' || $reserva->estado === 'en_curso') {
            return back()->withErrors(['cancelacion' => 'No se puede cancelar una reserva que ya ha sido ' . $reserva->estado . '.']);
        }

        $ahora = \Carbon\Carbon::now();
        $limiteCancelacion = \Carbon\Carbon::parse($reserva->fecha_inicio)->subDay();
        
        if ($ahora->gt($limiteCancelacion)) {
            return back()->withErrors(['cancelacion' => 'La reserva sólo puede cancelarse con mínimo 24 horas de anticipación.']);
        }

        $reserva->estado = 'cancelada';
        $reserva->save();

        $politicaCancelacion = $reserva->maquinaria->politica->tipo;
        Mail::to($cliente->email)->send(new ReservaCancelada($reserva, $politicaCancelacion));

        return back()->with('success', 'Reserva cancelada con éxito. Se ha enviado un correo con la política de cancelación.');
    }


    // Posibilidad de que el usuario pague una reserva pendiente desde el historial
    public function pagarDesdeHistorial($id_reserva)
    {
        $reserva = Reserva::find($id_reserva);

        if (!$reserva) {
            return back()->withErrors(['reserva' => 'Reserva no encontrada.']);
        }

        // Verifica que la reserva esté pendiente y pertenezca al usuario autenticado
        if ($reserva->estado !== 'pendiente' || $reserva->id_cliente !== Auth::id()) {
            return back()->withErrors(['reserva' => 'Solo se pueden pagar reservas pendientes.']);
        }

        // Establece el ID de la reserva en la sesión para que el PagoController la use
        session(['reserva_id' => $reserva->id_reserva]);

        return redirect()->route('pago.seleccionar')->with('success', 'Proceda a seleccionar el método de pago.');
    }

    /**
     * Muestra las reservas que están listas para ser entregadas.
     * (Estado: 'aprobada', fecha_inicio es hoy o ya pasó, y no está 'en_curso' o 'finalizada')
     *
     * @return \Illuminate\View\View
     */
    public function listasParaEntregar()
    {
        Log::info('Accediendo a listasParaEntregar.');

        // Obtener la fecha actual para comparaciones
        $today = Carbon::today();

        $reservasParaGestionEntrega = Reserva::with(['maquinaria', 'cliente'])
            ->where(function ($query) use ($today) {
                // Filtro para reservas 'aprobada'
                $query->where('estado', 'aprobada')
                      // La fecha de inicio debe ser hoy o anterior
                      ->whereDate('fecha_inicio', '<=', $today)
                      // Y la fecha de fin debe ser hoy o posterior
                      ->whereDate('fecha_fin', '>=', $today);
            })
            ->orWhere(function ($query) use ($today) {
                // Filtro para reservas 'en_curso'
                $query->where('estado', 'en_curso')
                      // Mostrar las en curso que terminan hoy o en el futuro, o las que terminaron en los últimos 7 días.
                      // Esto mantiene un registro reciente de las entregadas.
                      ->whereDate('fecha_fin', '>=', $today->subDays(7));
            })
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        Log::info('Reservas para gestión de entrega encontradas: ' . $reservasParaGestionEntrega->count());

        $layout = session('layout', 'layouts.empleado');
        return view('empleado.entregas-devoluciones', compact('reservasParaGestionEntrega', 'layout'));
    }
    /**
     * Registra la entrega de una maquinaria para una reserva.
     * Cambia el estado de la reserva a 'en_curso' y el de la maquinaria a 'alquilada'.
     *
     * @param Reserva $reserva
     * @param Request $request // Añadir Request para poder obtener el id_maquinaria_alternativa
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function registrarEntrega(Reserva $reserva, Request $request)
    {
        Log::info('Intentando registrar entrega para Reserva ID: ' . $reserva->id_reserva);
        $today = Carbon::today();

        try {
            // 1. Validar estado de la reserva
            if ($reserva->estado !== 'aprobada') {
                Log::warning('Intento de registrar entrega para reserva en estado incorrecto. ID: ' . $reserva->id_reserva . ', Estado: ' . $reserva->estado);
                return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'La reserva no está en estado "aprobada" para ser entregada.');
            }

            // 2. Validar fechas de entrega (debe ser en o después de fecha_inicio y antes o en fecha_fin)
            $fechaInicio = Carbon::parse($reserva->fecha_inicio);
            $fechaFin = Carbon::parse($reserva->fecha_fin);

            if ($today->lt($fechaInicio) || $today->gt($fechaFin)) {
                Log::warning('Intento de registrar entrega fuera del rango de fechas. ID: ' . $reserva->id_reserva . ', Hoy: ' . $today->format('Y-m-d') . ', Inicio: ' . $fechaInicio->format('Y-m-d') . ', Fin: ' . $fechaFin->format('Y-m-d'));
                return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'La entrega solo puede registrarse en o después de la fecha de inicio y en o antes de la fecha de fin de la reserva.');
            }

            // Obtener la maquinaria original de la reserva
            $maquinariaOriginal = $reserva->maquinaria;

            // ** NUEVA LÓGICA: Verificar estado de la maquinaria original **
            // Consideramos 'inactiva' y 'en_mantenimiento' como estados no disponibles para entrega
            if ($maquinariaOriginal && ($maquinariaOriginal->estado === 'alquilada' || $maquinariaOriginal->estado === 'inactiva' || $maquinariaOriginal->estado === 'en_mantenimiento')) {
                Log::info('Maquinaria original no disponible para Reserva ID: ' . $reserva->id_reserva . '. Estado: ' . $maquinariaOriginal->estado);
                
                // Buscar maquinarias alternativas disponibles en la misma localidad y del mismo tipo de uso
                // Asumiendo que quieres alternativas del mismo tipo (ej. retroexcavadora por retroexcavadora)
                $maquinariasAlternativas = Maquinaria::where('localidad_id', $maquinariaOriginal->localidad_id)
                                                     ->where('tipo_de_uso_id', $maquinariaOriginal->tipo_de_uso_id) // Mismo tipo de uso
                                                     ->where('estado', 'disponible')
                                                     ->where('id_maquinaria', '!=', $maquinariaOriginal->id_maquinaria) // Excluir la original
                                                     ->get();
                
                $layout = session('layout', 'layouts.empleado');
                return view('empleado.seleccionar-maquinaria-alternativa', compact('reserva', 'maquinariaOriginal', 'maquinariasAlternativas', 'layout'))
                       ->with('error', 'La maquinaria original no está disponible (' . $maquinariaOriginal->estado . '). Por favor, selecciona una alternativa o cancela la reserva.');
            }

            // Si la maquinaria original está disponible, proceder con la entrega normal
            // 3. Actualizar el estado de la reserva a 'en_curso' y asignar empleado
            $reserva->estado = 'en_curso';
            $reserva->id_empleado = Auth::id(); // Asigna el ID del empleado que registra la entrega
            $reserva->save();
            Log::info('Reserva ID ' . $reserva->id_reserva . ' actualizada a estado "en_curso".');

            // 4. Actualizar el estado de la maquinaria a 'alquilada'
            if ($maquinariaOriginal) {
                $maquinariaOriginal->estado = 'alquilada'; // Cambiar a 'alquilada'
                $maquinariaOriginal->save();
                Log::info('Maquinaria ID ' . $maquinariaOriginal->id_maquinaria . ' actualizada a estado "alquilada".');
            } else {
                Log::error('No se pudo encontrar la maquinaria asociada para la Reserva ID: ' . $reserva->id_reserva);
                return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'No se pudo encontrar la maquinaria asociada a la reserva.');
            }
            
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('success', 'Entrega registrada exitosamente. Maquinaria en curso de alquiler.');

        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar entrega para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'Error al registrar la entrega en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error inesperado al registrar entrega para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'Ocurrió un error inesperado al registrar la entrega: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la vista para seleccionar una maquinaria alternativa para una reserva.
     *
     * @param Reserva $reserva
     * @return \Illuminate\View\View
     */
    public function showAlternativeMachinerySelection(Reserva $reserva)
    {
        $layout = session('layout', 'layouts.empleado');
        $maquinariaOriginal = $reserva->maquinaria;

        // Buscar maquinarias alternativas disponibles en la misma localidad y del mismo tipo de uso
        $maquinariasAlternativas = Maquinaria::where('localidad_id', $maquinariaOriginal->localidad_id)
                                             ->where('tipo_de_uso_id', $maquinariaOriginal->tipo_de_uso_id) // Mismo tipo de uso
                                             ->where('estado', 'disponible')
                                             ->where('id_maquinaria', '!=', $maquinariaOriginal->id_maquinaria)
                                             ->get();

        return view('empleado.seleccionar-maquinaria-alternativa', compact('reserva', 'maquinariaOriginal', 'maquinariasAlternativas', 'layout'));
    }

    /**
     * Procesa la entrega de una reserva con una maquinaria alternativa seleccionada.
     *
     * @param Reserva $reserva
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processAlternativeDelivery(Reserva $reserva, Request $request)
    {
        $request->validate([
            'id_maquinaria_alternativa' => 'required|exists:maquinarias,id_maquinaria',
        ], [
            'id_maquinaria_alternativa.required' => 'Debes seleccionar una maquinaria alternativa.',
            'id_maquinaria_alternativa.exists' => 'La maquinaria alternativa seleccionada no es válida.',
        ]);

        $maquinariaAlternativa = Maquinaria::find($request->id_maquinaria_alternativa);

        try {
            // 1. Validar que la maquinaria alternativa esté disponible
            if ($maquinariaAlternativa->estado !== 'disponible') {
                return redirect()->route('empleado.seleccionar-maquinaria-alternativa', $reserva->id_reserva)
                                 ->with('error', 'La maquinaria alternativa seleccionada no está disponible.');
            }

            // 2. Actualizar la reserva con la nueva maquinaria
            $reserva->id_maquinaria = $maquinariaAlternativa->id_maquinaria;
            $reserva->estado = 'en_curso';
            $reserva->id_empleado = Auth::id();
            $reserva->save();
            Log::info('Reserva ID ' . $reserva->id_reserva . ' actualizada con maquinaria alternativa: ' . $maquinariaAlternativa->id_maquinaria . ' y estado "en_curso".');

            // 3. Actualizar el estado de la maquinaria alternativa a 'alquilada'
            $maquinariaAlternativa->estado = 'alquilada';
            $maquinariaAlternativa->save();
            Log::info('Maquinaria alternativa ID ' . $maquinariaAlternativa->id_maquinaria . ' actualizada a estado "alquilada".');

            // Opcional: Si la maquinaria original tenía un estado temporal (ej. 'reservada_para_entrega')
            // y no estaba realmente "alquilada" o "inactiva", podrías revertir su estado aquí.
            // Para este flujo, asumimos que 'alquilada' o 'inactiva' significa que no se puede usar.

            return redirect()->route('empleado.panel-entregas-devoluciones')->with('success', 'Entrega registrada con maquinaria alternativa exitosamente.');

        } catch (QueryException $e) {
            Log::error('Error de base de datos al procesar entrega con alternativa para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.seleccionar-maquinaria-alternativa', $reserva->id_reserva)->with('error', 'Error al procesar la entrega con maquinaria alternativa: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error inesperado al procesar entrega con alternativa para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.seleccionar-maquinaria-alternativa', $reserva->id_reserva)->with('error', 'Ocurrió un error inesperado al procesar la entrega: ' . $e->getMessage());
        }
    }


    /**
     * Muestra las reservas que están pendientes de devolución.
     * (Estado: 'en_curso', y fecha_fin es hoy o ya pasó)
     *
     * @return \Illuminate\View\View
     */
public function listasParaDevolver()
    {
        Log::info('Accediendo a listasParaDevolver.');

        // No se necesita $today para los filtros de estado, ya que no hay filtros de fecha.
        // Se mantiene para el orderBy si se quiere ordenar por fecha_fin.

        $reservasParaGestionDevolucion = Reserva::with(['maquinaria', 'cliente'])
            ->where('estado', 'en_curso') // Listar TODAS las reservas 'en_curso'
            ->orWhere('estado', 'finalizada') // Listar TODAS las reservas 'finalizada'
            ->orderBy('fecha_fin', 'asc') // Ordena por la fecha de fin, las más antiguas primero
            ->get();

        Log::info('Reservas para gestión de devolución encontradas: ' . $reservasParaGestionDevolucion->count());

        $layout = session('layout', 'layouts.empleado');
        return view('empleado.entregas-devoluciones', compact('reservasParaGestionDevolucion', 'layout'));
    }



    /**
     * Registra la devolución de una maquinaria para una reserva.
     * Cambia el estado de la reserva a 'finalizada' y el de la maquinaria a 'disponible'.
     *
     * @param Reserva $reserva
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registrarDevolucion(Reserva $reserva)
    {
        Log::info('Intentando registrar devolución para Reserva ID: ' . $reserva->id_reserva);
        $today = Carbon::today();

        try {
            // 1. Verificar si la reserva ya está finalizada o cancelada o no está en curso
            if ($reserva->estado !== 'en_curso') {
                Log::warning('Intento de registrar devolución para reserva en estado incorrecto. ID: ' . $reserva->id_reserva . ', Estado: ' . $reserva->estado);
                return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'La reserva no está en estado "en curso" para ser devuelta.');
            }

            // 2. Validar que la devolución se registre en o después de la fecha de inicio
            $fechaInicioReserva = Carbon::parse($reserva->fecha_inicio);
            if ($today->lt($fechaInicioReserva)) {
                Log::warning('Intento de devolver reserva cuya fecha de inicio aún no ha llegado. ID: ' . $reserva->id_reserva);
                return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'No se puede registrar la devolución de una maquinaria cuya reserva aún no ha comenzado.');
            }

            // 3. Actualizar el estado de la reserva a 'finalizada' y asignar empleado
            $reserva->estado = 'finalizada';
            $reserva->id_empleado = Auth::id(); // Asigna el ID del empleado que registra la devolución
            $reserva->save();
            Log::info('Reserva ID ' . $reserva->id_reserva . ' actualizada a estado "finalizada".');

            // 4. Actualizar el estado de la maquinaria a 'disponible'
            if ($reserva->maquinaria) {
                $reserva->maquinaria->estado = 'disponible';
                $reserva->maquinaria->save();
                Log::info('Maquinaria ID ' . $reserva->maquinaria->id_maquinaria . ' actualizada a estado "disponible".');
            } else {
                Log::error('No se pudo encontrar la maquinaria asociada para la Reserva ID: ' . $reserva->id_reserva);
                return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'No se pudo encontrar la maquinaria asociada a la reserva.');
            }

            return redirect()->route('empleado.panel-entregas-devoluciones')->with('success', 'Devolución registrada exitosamente. Reserva finalizada y maquinaria disponible.');

        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar devolución para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'Error al registrar la devolución en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error inesperado al registrar devolución para Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'Ocurrió un error inesperado al registrar la devolución: ' . $e->getMessage());
        }
    }

    public function cancelarDirecto(Reserva $reserva)
    {
        Log::info('Intentando cancelación DIRECTA para Reserva ID: ' . $reserva->id_reserva);

        // Solo permitimos cancelar si no está ya finalizada o cancelada
        if ($reserva->estado === 'finalizada' || $reserva->estado === 'cancelada') {
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'La reserva ya está ' . $reserva->estado . '. No se puede cancelar.');
        }

        try {
            $reserva->estado = 'cancelada';
            $reserva->id_empleado = Auth::id(); // Asigna el empleado que realiza la cancelación
            $reserva->save();
            Log::info('Reserva ID ' . $reserva->id_reserva . ' cancelada DIRECTAMENTE por empleado.');

            // Opcional: Enviar correo de cancelación (si la lógica lo requiere)
            // $cliente = $reserva->cliente;
            // $politicaCancelacion = $reserva->maquinaria->politica->tipo;
            // Mail::to($cliente->email)->send(new ReservaCancelada($reserva, $politicaCancelacion));

            return redirect()->route('empleado.panel-entregas-devoluciones')->with('success', 'Reserva cancelada directamente con éxito.');

        } catch (QueryException $e) {
            Log::error('Error de base de datos al cancelar directamente la Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'Error al cancelar la reserva en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error inesperado al cancelar directamente la Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'Ocurrió un error inesperado al cancelar la reserva: ' . $e->getMessage());
        }
    }
}

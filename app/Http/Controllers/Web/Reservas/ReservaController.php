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
        public function listasParaEntregar(Request $request) // Inject Request
    {
        Log::info('Accediendo a listasParaEntregar.');

        $today = Carbon::today();

        // --- Lógica para el Escenario Vacío ---
        if ($request->has('empty_scenario')) {
            Log::info('Modo de escenario vacío activado para Entregas.');
            $entregasPendientesHoyOProximas = collect(); // Colección vacía
            $entregasHistorial = collect();             // Colección vacía
        } else {
            // 1. Get reservations that are 'aprobada' (approved) and need to be delivered.
            // These are current or future deliveries.
            $entregasPendientesHoyOProximas = Reserva::with(['maquinaria', 'cliente'])
                ->where('estado', 'aprobada')
                ->where(function ($query) use ($today) {
                    $query->whereDate('fecha_inicio', '>=', $today)
                          ->orWhere(function ($q) use ($today) {
                              $q->whereDate('fecha_inicio', '<', $today)
                                ->whereDate('fecha_fin', '>=', $today);
                          });
                })
                ->orderBy('fecha_inicio', 'asc') // Order by the earliest start date
                ->get();

            // 2. Get reservations that are 'en_curso' or 'finalizada' (delivery history).
            // These are items that have already been delivered or completed.
            // We'll also include 'aprobada' reservations whose 'fecha_inicio' is in the past
            // and 'fecha_fin' is also in the past, meaning they expired without delivery.
            $entregasHistorial = Reserva::with(['maquinaria', 'cliente'])
                ->whereIn('estado', ['en_curso', 'finalizada', 'cancelada'])
                ->orWhere(function ($query) use ($today) {
                    $query->where('estado', 'aprobada')
                          ->whereDate('fecha_fin', '<', $today); // 'Aprobada' but expired without being delivered
                })
                ->orderBy('fecha_inicio', 'desc') // Order by the most recent deliveries first
                ->get();
        }
        // --- FIN Lógica para el Escenario Vacío ---

        Log::info('Reservas pendientes de entrega (hoy/próximas): ' . $entregasPendientesHoyOProximas->count());
        Log::info('Historial de entregas (en curso/finalizadas/expiradas): ' . $entregasHistorial->count());

        $layout = session('layout', 'layouts.empleado');

        return view('empleado.entregas-devoluciones', compact(
            'entregasPendientesHoyOProximas',
            'entregasHistorial',
            'layout',
            'today' // Still pass 'today' for any specific date checks in the view
        ));
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
                $maquinariasPotencialmenteAlternativas = Maquinaria::where('localidad_id', $maquinariaOriginal->localidad_id)
                                                     ->where('tipo_de_uso_id', $maquinariaOriginal->tipo_de_uso_id) // Mismo tipo de uso
                                                     ->where('estado', 'disponible')
                                                     ->where('id_maquinaria', '!=', $maquinariaOriginal->id_maquinaria) // Excluir la original
                                                     ->get();
                // Fechas para filtrar superposiciones
                $fechaInicioReservaActual = Carbon::parse($reserva->fecha_inicio);
                $fechaFinReservaActual = Carbon::parse($reserva->fecha_fin);

                // Luego, filtramos manualmente las que no tengan reservas superpuestas
                $maquinariasAlternativas = collect();
                
                foreach ($maquinariasPotencialmenteAlternativas as $maquinaria) {
                    $tieneReservasSuperpuestas = $maquinaria->reserva()
                        ->whereIn('estado', ['aprobada']) // o más estados si querés
                        ->where(function ($query) use ($fechaInicioReservaActual, $fechaFinReservaActual) {
                            $query->where('fecha_inicio', '<=', $fechaFinReservaActual)
                                ->where('fecha_fin', '>=', $fechaInicioReservaActual);
                        })
                        ->exists();

                    if (!$tieneReservasSuperpuestas) {
                        $maquinariasAlternativas->push($maquinaria);
                    }
                }
                
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
            
            return redirect()->route('empleado.entregas-pendientes')->with('success', 'Entrega registrada exitosamente. Maquinaria en curso de alquiler.');

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

            return redirect()->route('empleado.entregas-pendientes')->with('success', 'Entrega registrada con maquinaria alternativa exitosamente.');

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
public function listasParaDevolver(Request $request) // Inject Request
    {
        Log::info('Accediendo a listasParaDevolver.');

        // --- Lógica para el Escenario Vacío ---
        if ($request->has('empty_scenario')) {
            Log::info('Modo de escenario vacío activado para Devoluciones.');
            $devolucionesPendientes = collect(); // Colección vacía
            $devolucionesHistorial = collect();  // Colección vacía
        } else {
            // 1. Obtener las reservas "en_curso" (pendientes de devolver)
            $devolucionesPendientes = Reserva::with(['maquinaria', 'cliente'])
                ->where('estado', 'en_curso')
                ->orderBy('fecha_fin', 'asc') // Las más próximas a vencer/pasar primero
                ->get();

            // 2. Obtener las reservas "finalizada" (historial de devoluciones)
            $devolucionesHistorial = Reserva::with(['maquinaria', 'cliente'])
                ->where('estado', 'finalizada')
                ->orderBy('fecha_fin', 'desc') // Las más recientes primero en el historial
                ->get();
        }
        // --- FIN Lógica para el Escenario Vacío ---

        Log::info('Reservas pendientes de devolución: ' . $devolucionesPendientes->count());
        Log::info('Historial de devoluciones: ' . $devolucionesHistorial->count());

        $layout = session('layout', 'layouts.empleado');

        return view('empleado.entregas-devoluciones', compact(
            'devolucionesPendientes',
            'devolucionesHistorial',
            'layout'
        ));
    }



    /**
     * Registra la devolución de una maquinaria para una reserva.
     * Cambia el estado de la reserva a 'finalizada' y el de la maquinaria a 'disponible'.
     *
     * @param Reserva $reserva
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registrarDevolucion(Request $request, Reserva $reserva)
    {
        try {
            // Verificar el estado actual de la reserva
            if ($reserva->estado !== 'en_curso') {
                return back()->with('error', 'La reserva no está en estado "en curso" para registrar la devolución.');
            }

            $fechaDevolucionReal = Carbon::today(); // Asumimos que la devolución se registra hoy

            $recargoPorDemora = 0;
            $diasDemora = 0;

            // Calcular si hay recargo por demora
            // Si la fecha de fin de la reserva es anterior a la fecha de devolución real (hoy)
            if (Carbon::parse($reserva->fecha_fin)->lt($fechaDevolucionReal)) {
                $diasDemora = Carbon::parse($reserva->fecha_fin)->diffInDays($fechaDevolucionReal);

                // Cargar la maquinaria asociada para obtener su precio_dia
                // Se asume que $reserva->maquinaria ya carga la maquinaria, gracias a tu modelo Reserva y la relación.
                $maquinaria = $reserva->maquinaria; 

                // *** CORRECCIÓN AQUÍ: USAR $maquinaria->precio_dia ***
                if ($maquinaria && isset($maquinaria->precio_dia)) { 
                    // Valor por día x 1.5
                    $valorDiarioConRecargo = $maquinaria->precio_dia * 1.5;
                    $recargoPorDemora = $valorDiarioConRecargo * $diasDemora;
                } else {
                    Log::warning("Maquinaria o precio_dia no encontrado para el cálculo de recargo de Reserva ID: {$reserva->id_reserva}");
                    // Puedes decidir si abortar o continuar sin recargo si no se puede calcular
                }
            }

            // Si hay recargo, redirigimos a una página de confirmación/detalle
            if ($recargoPorDemora > 0) {
                return redirect()->route('confirmar-devolucion-recargo', [
                    'reserva_id' => $reserva->id_reserva,
                    'recargo' => $recargoPorDemora,
                    'dias_demora' => $diasDemora,
                    'fecha_fin_original' => Carbon::parse($reserva->fecha_fin)->format('d/m/Y'),
                    'fecha_devolucion_real' => $fechaDevolucionReal->format('d/m/Y')
                ]);
            }

            // Si no hay recargo, o si se decide no implementarlo y simplemente registrar,
            // procedemos con la devolución normal.
            $reserva->estado = 'finalizada';
            // Opcional: registrar la fecha de devolución real si tienes un campo para ello
            // $reserva->fecha_devolucion_real = $fechaDevolucionReal;
            $reserva->save();

            if ($reserva->maquinaria) {
                 $reserva->maquinaria->estado = 'disponible';
                 $reserva->maquinaria->save();
            }

            Log::info("Devolución de reserva registrada con éxito: ID {$reserva->id_reserva}.");
            return back()->with('success', 'Devolución de maquinaria registrada con éxito (sin recargo).');
            

        } catch (QueryException $e) {
            Log::error('Error de base de datos al registrar devolución: ' . $e->getMessage(), ['reserva_id' => $reserva->id_reserva]);
            return back()->with('error', 'Hubo un error de base de datos al registrar la devolución.');
        } catch (\Exception $e) {
            Log::error('Error al registrar devolución: ' . $e->getMessage(), ['reserva_id' => $reserva->id_reserva]);
            return back()->with('error', 'Hubo un error al registrar la devolución: ' . $e->getMessage());
        }
    }

    public function confirmarDevolucionConRecargo(Request $request) // <-- ¡Asegúrate de que este nombre sea EXACTO!
    {
        // Se espera que los datos del recargo vengan en la URL (query parameters)
        $reservaId = $request->query('reserva_id');
        $recargo = $request->query('recargo');
        $diasDemora = $request->query('dias_demora');
        $fechaFinOriginal = $request->query('fecha_fin_original');
        $fechaDevolucionReal = $request->query('fecha_devolucion_real');

        // Opcional: Recargar la reserva para mostrar más detalles
        $reserva = Reserva::with(['maquinaria', 'cliente'])->find($reservaId);

        if (!$reserva) {
            return redirect()->route('empleado.devoluciones-pendientes')->with('error', 'Reserva no encontrada para confirmar devolución.');
        }

        $layout = session('layout', 'layouts.empleado');
        return view('empleado.confirmar-devolucion-recargo', compact('reserva', 'recargo', 'diasDemora', 'fechaFinOriginal', 'fechaDevolucionReal', 'layout'));
    }

    public function finalizarDevolucion(Request $request, Reserva $reserva) // <-- ¡ASEGÚRATE DE QUE ESTE NOMBRE SEA EXACTO!
    {
        try {
            // Asegurarse de que la reserva esté en estado 'en_curso' antes de finalizarla
            if ($reserva->estado !== 'en_curso') {
                return back()->with('error', 'La reserva no está en estado "en curso" para finalizar la devolución.');
            }

            $reserva->estado = 'finalizada';
            // Opcional: registrar la fecha de devolución real si tienes un campo para ello
            // $reserva->fecha_devolucion_real = Carbon::today();
            $reserva->save();

            // Opcional: Actualizar el estado de la maquinaria a 'disponible'
            // if ($reserva->maquinaria) {
            //     $reserva->maquinaria->estado = 'disponible';
            //     $reserva->maquinaria->save();
            // }

            Log::info("Devolución de reserva finalizada (con recargo) con éxito: ID {$reserva->id_reserva}.");
            return redirect()->route('empleado.devoluciones-pendientes')->with('success', 'Devolución registrada y recargo informado con éxito.');

        } catch (QueryException $e) {
            Log::error('Error de base de datos al finalizar devolución (recargo): ' . $e->getMessage(), ['reserva_id' => $reserva->id_reserva]);
            return back()->with('error', 'Hubo un error de base de datos al finalizar la devolución.');
        } catch (\Exception $e) {
            Log::error('Error al finalizar devolución (recargo): ' . $e->getMessage(), ['reserva_id' => $reserva->id_reserva]);
            return back()->with('error', 'Hubo un error al finalizar la devolución: ' . $e->getMessage());
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
            // **Paso 1: Calcular el monto a reembolsar ANTES de cambiar el estado de la reserva**
            // Asegúrate de que las fechas estén en formato Carbon para el cálculo.
            // Las fechas en el modelo Reserva son strings, hay que convertirlas a objetos Carbon.
            $fechaInicio = Carbon::parse($reserva->fecha_inicio);
            $fechaFin = Carbon::parse($reserva->fecha_fin);

            // Calcular la cantidad de días (incluyendo el día de inicio y fin si la política lo considera,
            // normalmente es la diferencia en días, +1 si el último día es completo)
            // Carbon::diffInDays() calcula la diferencia estricta. Para días "completos" de reserva, a menudo se añade 1.
            // Si una reserva del 01/01 al 01/01 es 1 día, entonces es diffInDays + 1.
            // Si del 01/01 al 02/01 son 2 días, entonces es diffInDays + 1.
            $diasReserva = $fechaInicio->diffInDays($fechaFin) + 1;

            // Obtener el precio por día de la maquinaria relacionada con la reserva
            // Asegúrate de que la relación 'maquinaria' esté cargada o se pueda cargar.
            // Puedes usar ->load('maquinaria') si no estás seguro de que ya está cargada.
            $precioPorDia = $reserva->maquinaria->precio_dia;

            $montoAReembolsar = $diasReserva * $precioPorDia;

            // **Paso 2: Proceder con la cancelación de la reserva**
            $reserva->estado = 'cancelada';
            $reserva->id_empleado = Auth::id(); // Asigna el empleado que realiza la cancelación
            $reserva->save();
            Log::info('Reserva ID ' . $reserva->id_reserva . ' cancelada DIRECTAMENTE por empleado.');

            // Opcional: Enviar correo de cancelación (si la lógica lo requiere)
            // $cliente = $reserva->cliente;
            // $politicaCancelacion = $reserva->maquinaria->politica->tipo;
            // Mail::to($cliente->email)->send(new ReservaCancelada($reserva, $politicaCancelacion));

            // **Paso 3: Retornar el mensaje de éxito incluyendo el monto**
            // Usamos number_format para un formato de moneda amigable (ej. 1.234,50)
            $mensajeExito = 'Reserva cancelada directamente con éxito. Monto a reembolsar: $' . number_format($montoAReembolsar, 2, ',', '.');
            return redirect()->route('empleado.entregas-pendientes')->with('success', $mensajeExito);

        } catch (QueryException $e) {
            Log::error('Error de base de datos al cancelar directamente la Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'Error al cancelar la reserva en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error inesperado al cancelar directamente la Reserva ID ' . $reserva->id_reserva . ': ' . $e->getMessage());
            return redirect()->route('empleado.panel-entregas-devoluciones')->with('error', 'Ocurrió un error inesperado al cancelar la reserva: ' . $e->getMessage());
        }
    }
}

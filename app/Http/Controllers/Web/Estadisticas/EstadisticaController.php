<?php

namespace App\Http\Controllers\Web\Estadisticas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Domain\User\Models\Usuario;
use App\Enums\Roles;
use Carbon\CarbonPeriod;
use App\Domain\Pago\Models\Pago;
use App\Domain\Reserva\Models\Reserva; // Importa el modelo Reserva
use App\Domain\Maquinaria\Models\Maquinaria; // Importa el modelo Maquinaria

class EstadisticaController extends Controller
{
    /**
     * Muestra la vista principal de estadísticas para los administradores.
     * En el futuro, aquí se recopilarán todos los datos necesarios para las estadísticas.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */

        public function showNewClientsStatistics(Request $request)
    {
        $layout = session('layout', 'layouts.admin');
        $nuevosClientesCount = null;
        $chartLabels = []; // Etiquetas para el eje X del gráfico (ej: Ene, Feb o Semana 01-07)
        $chartData = [];   // Datos para el eje Y del gráfico (ej: cantidad de clientes)

        $fechaInicioStr = $request->input('fecha_inicio');
        $fechaFinStr = $request->input('fecha_fin');
        // Nuevo parámetro: tipo de período (mes o semana), por defecto 'month'
        $periodType = $request->input('period_type', 'month'); 

        // Valores por defecto si no se han seleccionado fechas
        if (!$fechaInicioStr) {
            $fechaInicioStr = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d'); // Últimos 6 meses
        }
        if (!$fechaFinStr) {
            $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d'); // Hasta fin del mes actual
        }

        try {
            $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
            $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

            // Regla de Negocio 1: Contar nuevos clientes en el rango de fechas
            $nuevosClientesCount = Usuario::where('rol', Roles::CLIENTE->value)
                                      ->whereBetween('fecha_alta', [$fechaInicio, $fechaFin])
                                      ->count();

            // Lógica para el gráfico de barras: Clientes por mes o por semana
            $clientesPorFecha = Usuario::where('rol', Roles::CLIENTE->value)
                                       ->whereBetween('fecha_alta', [$fechaInicio, $fechaFin])
                                       ->select('fecha_alta')
                                       ->get();

            $groupedCounts = [];

            if ($periodType === 'week') {
                // Generar período semanal
                $period = CarbonPeriod::create($fechaInicio->startOfWeek(), '1 week', $fechaFin->endOfWeek());
                foreach ($period as $date) {
                    // Formato para la semana: "Semana XX (YYYY)" o "dd/mm - dd/mm"
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' (' . $date->year . ')';
                    // O un formato más legible para el rango de fechas de la semana
                    // $weekLabel = $date->startOfWeek()->format('d/m') . ' - ' . $date->endOfWeek()->format('d/m');
                    $chartLabels[] = $weekLabel;
                    $groupedCounts[$weekLabel] = 0;
                }

                foreach ($clientesPorFecha as $cliente) {
                    $date = Carbon::parse($cliente->fecha_alta);
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' (' . $date->year . ')';
                    // $weekLabel = $date->startOfWeek()->format('d/m') . ' - ' . $date->endOfWeek()->format('d/m');
                    if (isset($groupedCounts[$weekLabel])) {
                        $groupedCounts[$weekLabel]++;
                    }
                }
            } else { // Default: 'month'
                // Generar período mensual
                $period = CarbonPeriod::create($fechaInicio->startOfMonth(), '1 month', $fechaFin->endOfMonth());
                foreach ($period as $date) {
                    $monthYear = $date->format('M Y'); // Ej: "Jul 2025"
                    $chartLabels[] = $monthYear;
                    $groupedCounts[$monthYear] = 0;
                }

                foreach ($clientesPorFecha as $cliente) {
                    $monthYear = Carbon::parse($cliente->fecha_alta)->format('M Y');
                    if (isset($groupedCounts[$monthYear])) {
                        $groupedCounts[$monthYear]++;
                    }
                }
            }

            // Convertir los conteos a un array indexado para Chart.js
            $chartData = array_values($groupedCounts);

        } catch (\Exception $e) {
            \Log::error('Error al obtener estadísticas de nuevos clientes: ' . $e->getMessage());
            $nuevosClientesCount = 0;
            $chartLabels = [];
            $chartData = [];
            session()->flash('error', 'Hubo un error al procesar las fechas o los datos para el gráfico. Inténtalo de nuevo.');
        }
        
        // Pasa las variables a la vista, incluyendo los datos para el gráfico y el tipo de período seleccionado
        return view('estadisticas.new-clients', compact('layout', 'nuevosClientesCount', 'chartLabels', 'chartData', 'periodType'));
    }

    /**
     * Muestra la vista de estadísticas para Maquinarias Más Alquiladas.
     * Lógica a implementar más adelante.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showMostRentedMachineryStatistics(Request $request)
    {
        $layout = session('layout', 'layouts.admin');
        $mostRentedMachinery = []; // Array para almacenar las máquinas más alquiladas
        $chartLabels = []; // Etiquetas para el gráfico (nombres de maquinaria)
        $chartData = [];   // Datos para el gráfico (cantidad de alquileres)

        $fechaInicioStr = $request->input('fecha_inicio');
        $fechaFinStr = $request->input('fecha_fin');

        // Valores por defecto para el rango de fechas si no se proporcionan
        if (!$fechaInicioStr) {
            $fechaInicioStr = Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d'); // Últimos 12 meses por defecto
        }
        if (!$fechaFinStr) {
            $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d'); // Hasta fin del mes actual
        }

        try {
            $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
            $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

            // Obtener las reservas en el rango de fechas
            // Agrupar por id_maquinaria y contar las ocurrencias
            $rentals = Reserva::selectRaw('id_maquinaria, COUNT(*) as count') // Seleccionar el conteo como 'count'
                               ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                               ->groupBy('id_maquinaria')
                               ->orderByDesc('count') // Usar orderByDesc con el alias 'count'
                               ->limit(10) // Limitar a las 10 maquinarias más alquiladas
                               ->get();

            // Iterar sobre los resultados para obtener los nombres de las maquinarias y preparar los datos del gráfico
            foreach ($rentals as $rental) {
                $maquinaria = Maquinaria::find($rental->id_maquinaria);
                if ($maquinaria) {
                    // Combinar marca y modelo para el nombre
                    $nombreMaquinaria = $maquinaria->marca . ' ' . $maquinaria->modelo; 
                    
                    $mostRentedMachinery[] = [
                        'nombre' => $nombreMaquinaria,
                        'cantidad_alquileres' => $rental->count,
                    ];
                    $chartLabels[] = $nombreMaquinaria;
                    $chartData[] = $rental->count;
                }
            }

        } catch (\Exception $e) {
            \Log::error('Error al obtener estadísticas de maquinarias más alquiladas: ' . $e->getMessage());
            $mostRentedMachinery = [];
            $chartLabels = [];
            $chartData = [];
            session()->flash('error', 'Hubo un error al procesar los datos de maquinarias más alquiladas. Inténtalo de nuevo.');
        }

        return view('estadisticas.most-rented-machinery', compact('layout', 'mostRentedMachinery', 'chartLabels', 'chartData', 'fechaInicioStr', 'fechaFinStr'));
    }

    /**
     * Muestra la vista de estadísticas para Ingresos.
     * Lógica a implementar más adelante.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showIncomeStatistics(Request $request)
    {
        $layout = session('layout', 'layouts.admin');
        $totalIncome = 0;
        $chartLabels = [];
        $chartData = [];

        $fechaInicioStr = $request->input('fecha_inicio');
        $fechaFinStr = $request->input('fecha_fin');
        $periodType = $request->input('period_type', 'month'); // Por defecto 'month'

        // Valores por defecto para el rango de fechas si no se proporcionan
        if (!$fechaInicioStr) {
            $fechaInicioStr = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
        }
        if (!$fechaFinStr) {
            $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        try {
            $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
            $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

            // Obtener pagos completados en el rango de fechas
            // Asumiendo que 'estado_pago' tiene un valor para pagos exitosos, por ejemplo 'completado' o 'aprobado'
            $pagos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                         ->where('estado_pago', 'completo') // Ajusta 'aprobado' al estado real de tus pagos completados
                         ->get();

            // Calcular el monto total de ingresos
            $totalIncome = $pagos->sum('monto');

            // Lógica para el gráfico de barras: Ingresos por mes o por semana
            $groupedIncome = [];

            if ($periodType === 'week') {
                $period = CarbonPeriod::create($fechaInicio->startOfWeek(), '1 week', $fechaFin->endOfWeek());
                foreach ($period as $date) {
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' (' . $date->year . ')';
                    $chartLabels[] = $weekLabel;
                    $groupedIncome[$weekLabel] = 0;
                }

                foreach ($pagos as $pago) {
                    $date = Carbon::parse($pago->fecha_pago);
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' (' . $date->year . ')';
                    if (isset($groupedIncome[$weekLabel])) {
                        $groupedIncome[$weekLabel] += $pago->monto;
                    }
                }
            } else { // Default: 'month'
                $period = CarbonPeriod::create($fechaInicio->startOfMonth(), '1 month', $fechaFin->endOfMonth());
                foreach ($period as $date) {
                    $monthYear = $date->format('M Y');
                    $chartLabels[] = $monthYear;
                    $groupedIncome[$monthYear] = 0;
                }

                foreach ($pagos as $pago) {
                    $monthYear = Carbon::parse($pago->fecha_pago)->format('M Y');
                    if (isset($groupedIncome[$monthYear])) {
                        $groupedIncome[$monthYear] += $pago->monto;
                    }
                }
            }

            $chartData = array_values($groupedIncome);

        } catch (\Exception $e) {
            \Log::error('Error al obtener estadísticas de ingresos: ' . $e->getMessage());
            $totalIncome = 0;
            $chartLabels = [];
            $chartData = [];
            session()->flash('error', 'Hubo un error al procesar los datos de ingresos. Inténtalo de nuevo.');
        }

        return view('estadisticas.income', compact('layout', 'totalIncome', 'chartLabels', 'chartData', 'periodType'));
    }
}
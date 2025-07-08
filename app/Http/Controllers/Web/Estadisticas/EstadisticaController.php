<?php

namespace App\Http\Controllers\Web\Estadisticas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\User\Models\Usuario;
use App\Domain\Pago\Models\Pago;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\Maquinaria\Models\Maquinaria;
use App\Enums\Roles;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EstadisticaController extends Controller
{
    /**
     * Muestra la vista de estadísticas para Nuevos Clientes Registrados.
     * Incluye datos para el conteo total y para un gráfico de barras.
     * Permite seleccionar si el gráfico se muestra por semana o por mes.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showNewClientsStatistics(Request $request)
    {
        $layout = session('layout', 'layouts.admin');
        $nuevosClientesCount = null;
        $chartLabels = [];
        $chartData = [];

        $fechaInicioStr = $request->input('fecha_inicio');
        $fechaFinStr = $request->input('fecha_fin');
        $periodType = $request->input('period_type', 'month'); 

        // Valores por defecto si no se han seleccionado fechas
        if (!$fechaInicioStr) {
            $fechaInicioStr = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
        }
        if (!$fechaFinStr) {
            $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        try {
            $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
            $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

            // ** VALIDACIÓN: Fecha de inicio debe ser menor o igual a la fecha de fin **
            if ($fechaInicio->greaterThan($fechaFin)) {
                session()->flash('error', 'La fecha de inicio no puede ser posterior a la fecha de fin.');
                return view('admin.statistics.new-clients', compact('layout', 'nuevosClientesCount', 'chartLabels', 'chartData', 'periodType'));
            }

            $nuevosClientesCount = Usuario::where('rol', Roles::CLIENTE->value)
                                      ->whereBetween('fecha_alta', [$fechaInicio, $fechaFin])
                                      ->count();

            $clientesPorFecha = Usuario::where('rol', Roles::CLIENTE->value)
                                       ->whereBetween('fecha_alta', [$fechaInicio, $fechaFin])
                                       ->select('fecha_alta')
                                       ->get();

            $groupedCounts = [];

            if ($periodType === 'week') {
                $period = CarbonPeriod::create($fechaInicio->startOfWeek(), '1 week', $fechaFin->endOfWeek());
                foreach ($period as $date) {
                    // ** ETIQUETA DE SEMANA MEJORADA **
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M'); // Ej: Semana 1 de Ene
                    $chartLabels[] = $weekLabel;
                    $groupedCounts[$weekLabel] = 0;
                }

                foreach ($clientesPorFecha as $cliente) {
                    $date = Carbon::parse($cliente->fecha_alta);
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M');
                    if (isset($groupedCounts[$weekLabel])) {
                        $groupedCounts[$weekLabel]++;
                    }
                }
            } else { // Default: 'month'
                $period = CarbonPeriod::create($fechaInicio->startOfMonth(), '1 month', $fechaFin->endOfMonth());
                foreach ($period as $date) {
                    $monthYear = $date->format('M Y');
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

            $chartData = array_values($groupedCounts);

        } catch (\Exception $e) {
            \Log::error('Error al obtener estadísticas de nuevos clientes: ' . $e->getMessage());
            $nuevosClientesCount = 0;
            $chartLabels = [];
            $chartData = [];
            session()->flash('error', 'Hubo un error al procesar las fechas o los datos para el gráfico. Inténtalo de nuevo.');
        }
        
        return view('estadisticas.new-clients', compact('layout', 'nuevosClientesCount', 'chartLabels', 'chartData', 'periodType'));
    }

    /**
     * Muestra la vista de estadísticas para Maquinarias Más Alquiladas.
     * Calcula las maquinarias más alquiladas en un período dado.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showMostRentedMachineryStatistics(Request $request)
    {
        $layout = session('layout', 'layouts.admin');
        $mostRentedMachinery = [];
        $chartLabels = [];
        $chartData = [];

        $fechaInicioStr = $request->input('fecha_inicio');
        $fechaFinStr = $request->input('fecha_fin');

        // Valores por defecto para el rango de fechas si no se proporcionan
        if (!$fechaInicioStr) {
            $fechaInicioStr = Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d');
        }
        if (!$fechaFinStr) {
            $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        try {
            $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
            $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

            // ** VALIDACIÓN: Fecha de inicio debe ser menor o igual a la fecha de fin **
            if ($fechaInicio->greaterThan($fechaFin)) {
                session()->flash('error', 'La fecha de inicio no puede ser posterior a la fecha de fin.');
                return view('estadisticas.most-rented-machinery', compact('layout', 'mostRentedMachinery', 'chartLabels', 'chartData', 'fechaInicioStr', 'fechaFinStr'));
            }

            $rentals = Reserva::selectRaw('id_maquinaria, COUNT(*) as count')
                               ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                               ->groupBy('id_maquinaria')
                               ->orderByDesc('count')
                               ->limit(10)
                               ->get();

            foreach ($rentals as $rental) {
                $maquinaria = Maquinaria::find($rental->id_maquinaria);
                if ($maquinaria) {
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
     * Calcula el monto total de pagos y agrupa por semana o mes para un gráfico.
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
        $periodType = $request->input('period_type', 'month');

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

            // ** VALIDACIÓN: Fecha de inicio debe ser menor o igual a la fecha de fin **
            if ($fechaInicio->greaterThan($fechaFin)) {
                session()->flash('error', 'La fecha de inicio no puede ser posterior a la fecha de fin.');
                return view('admin.statistics.income', compact('layout', 'totalIncome', 'chartLabels', 'chartData', 'periodType'));
            }

            $pagos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                         ->where('estado_pago', 'completo')
                         ->get();

            $totalIncome = $pagos->sum('monto');

            $groupedIncome = [];

            if ($periodType === 'week') {
                $period = CarbonPeriod::create($fechaInicio->startOfWeek(), '1 week', $fechaFin->endOfWeek());
                foreach ($period as $date) {
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M');
                    $chartLabels[] = $weekLabel;
                    $groupedIncome[$weekLabel] = 0;
                }

                foreach ($pagos as $pago) {
                    $date = Carbon::parse($pago->fecha_pago);
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M');
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
                        // ** CORRECCIÓN AQUÍ: Sumar el monto en lugar de solo contar **
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

<?php

namespace App\Http\Controllers\Web\Estadisticas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Domain\User\Models\Usuario;
use App\Enums\Roles;
use Carbon\CarbonPeriod;

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
        // Aquí iría la lógica para obtener las máquinas más alquiladas
        // Por ahora, solo devolveremos una vista simple.
        return view('estadisticas.most-rented-machinery', compact('layout'));
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
        // Aquí iría la lógica para obtener los ingresos
        // Por ahora, solo devolveremos una vista simple.
        return view('estadisticas.income', compact('layout'));
    }
}
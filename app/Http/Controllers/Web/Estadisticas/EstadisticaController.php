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
use Barryvdh\DomPDF\Facade\Pdf; 
use Faker\Factory as Faker;

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

    if (!$fechaInicioStr) {
        $fechaInicioStr = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
    }
    if (!$fechaFinStr) {
        $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    try {
        $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
        $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

        if ($fechaInicio->greaterThan($fechaFin)) {
            return redirect()->route('admin.estadisticas.nuevos-clientes')
            ->with('error', 'La fecha de inicio no puede ser posterior a la fecha de fin.');
            
        } else {
            // Comparamos las fechas actuales con las de la sesión
            $fechasGuardadas = session('fechas_rango');
            $fechasHanCambiado = false;

            if (!$fechasGuardadas || 
                $fechasGuardadas['inicio'] !== $fechaInicio->timestamp || 
                $fechasGuardadas['fin'] !== $fechaFin->timestamp) 
            {
                $fechasHanCambiado = true;
                // Borramos los datos viejos si las fechas han cambiado
                session()->forget('fechas_clientes');
            }

            // --- INICIO: LÓGICA DE DATOS FICTICIOS ---
            if ($fechasHanCambiado) {
                $nuevosClientesCount = rand(50, 500);
                $fechasFicticias = [];
                for ($i = 0; $i < $nuevosClientesCount; $i++) {
                    $fechasFicticias[] = Carbon::createFromTimestamp(
                        rand($fechaInicio->timestamp, $fechaFin->timestamp)
                    );
                }

                $noClientsStart = Carbon::parse('2025-01-20')->startOfDay();
                $noClientsEnd = Carbon::parse('2025-02-20')->endOfDay();

                $fechasFicticias = array_filter($fechasFicticias, function ($fecha) use ($noClientsStart, $noClientsEnd) {
                    return !$fecha->between($noClientsStart, $noClientsEnd);
                });

                // Guardamos los nuevos datos y el rango de fechas en la sesión
                session([
                    'fechas_clientes' => $fechasFicticias,
                    'fechas_rango' => ['inicio' => $fechaInicio->timestamp, 'fin' => $fechaFin->timestamp]
                ]);

            } else {
                // Si las fechas son las mismas, usamos los datos de la sesión
                $fechasFicticias = session('fechas_clientes');
            }
            // --- FIN: LÓGICA DE DATOS FICTICIOS ---

            $nuevosClientesCount = count($fechasFicticias);
            $groupedCounts = [];
            $chartLabels = [];

            if ($nuevosClientesCount > 0) {
                if ($periodType === 'week') {
                    $period = CarbonPeriod::create($fechaInicio->startOfWeek(), '1 week', $fechaFin->endOfWeek());
                    foreach ($period as $date) {
                        $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M Y');
                        $chartLabels[] = $weekLabel;
                        $groupedCounts[$weekLabel] = 0;
                    }
                    foreach ($fechasFicticias as $fecha) {
                        $date = Carbon::parse($fecha);
                        $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M Y');
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
                    foreach ($fechasFicticias as $fecha) {
                        $monthYear = Carbon::parse($fecha)->format('M Y');
                        if (isset($groupedCounts[$monthYear])) {
                            $groupedCounts[$monthYear]++;
                        }
                    }
                }
                $chartData = array_values($groupedCounts);
            }
        }
    } catch (\Exception $e) {
        \Log::error('Error al generar estadísticas ficticias de nuevos clientes: ' . $e->getMessage());
        $nuevosClientesCount = 0;
        $chartLabels = [];
        $chartData = [];
        session()->flash('error', 'Hubo un error al procesar las fechas o los datos para el gráfico. Inténtalo de nuevo.');
        session()->forget(['fechas_clientes', 'fechas_rango']);
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
    $alquileresFicticios = [];
    $layout = session('layout', 'layouts.admin');
    $mostRentedMachinery = [];
    $chartLabels = [];
    $chartData = [];

    $fechaInicioStr = $request->input('fecha_inicio');
    $fechaFinStr = $request->input('fecha_fin');

    if (!$fechaInicioStr) {
        $fechaInicioStr = Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d');
    }
    if (!$fechaFinStr) {
        $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    try {
        $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
        $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

        if ($fechaInicio->greaterThan($fechaFin)) {
            return redirect()->route('admin.estadisticas.maquinas-mas-alquiladas')
            ->with('error', 'La fecha de inicio no puede ser posterior a la fecha de fin.');
        } else {
            $fechasGuardadas = session('fechas_rango_maquinas');
            $fechasHanCambiado = false;

            if (!$fechasGuardadas || 
                $fechasGuardadas['inicio'] !== $fechaInicio->timestamp || 
                $fechasGuardadas['fin'] !== $fechaFin->timestamp) 
            {
                $fechasHanCambiado = true;
                session()->forget('alquileres_ficticios');
            }

            if ($fechasHanCambiado) {
                $faker = Faker::create();
                
                $maquinarias = Maquinaria::all(['id_maquinaria', 'marca', 'modelo']);
                // CORRECCIÓN 1: Obtener IDs de la columna correcta
                $idsMaquinarias = $maquinarias->pluck('id_maquinaria')->toArray();
                
                $alquileresFicticios = [];
                $numAlquileres = rand(500, 2000);
                
                for ($i = 0; $i < $numAlquileres; $i++) {
                    $fechaAlquiler = Carbon::createFromTimestamp(
                        $faker->numberBetween($fechaInicio->timestamp, $fechaFin->timestamp)
                    );
                    $maquinariaId = $faker->randomElement($idsMaquinarias);
                    $alquileresFicticios[] = (object)['id_maquinaria' => $maquinariaId, 'fecha_inicio' => $fechaAlquiler];
                }

                $noRentalsStart = Carbon::parse('2025-01-20')->startOfDay();
                $noRentalsEnd = Carbon::parse('2025-02-20')->endOfDay();

                $alquileresFicticios = array_filter($alquileresFicticios, function ($alquiler) use ($noRentalsStart, $noRentalsEnd) {
                    return !$alquiler->fecha_inicio->between($noRentalsStart, $noRentalsEnd);
                });

                session([
                    'alquileres_ficticios' => $alquileresFicticios,
                    'fechas_rango_maquinas' => ['inicio' => $fechaInicio->timestamp, 'fin' => $fechaFin->timestamp]
                ]);

            } else {
                $alquileresFicticios = session('alquileres_ficticios');
            }
            
            $conteoAlquileres = [];
            foreach ($alquileresFicticios as $alquiler) {
                $id = $alquiler->id_maquinaria;
                $conteoAlquileres[$id] = ($conteoAlquileres[$id] ?? 0) + 1;
            }

            arsort($conteoAlquileres);
            $top10Ids = array_slice($conteoAlquileres, 0, 10, true);

            // CORRECCIÓN 2: Obtener los nombres de las maquinarias usando la clave correcta
            $maquinariasTop10 = Maquinaria::whereIn('id_maquinaria', array_keys($top10Ids))->get()->keyBy('id_maquinaria');
            
            foreach ($top10Ids as $id_maquinaria => $count) {
                if (isset($maquinariasTop10[$id_maquinaria])) {
                    $maquinaria = $maquinariasTop10[$id_maquinaria];
                    $nombreMaquinaria = $maquinaria->marca . ' ' . $maquinaria->modelo;

                    $mostRentedMachinery[] = [
                        'nombre' => $nombreMaquinaria,
                        'cantidad_alquileres' => $count,
                    ];
                    $chartLabels[] = $nombreMaquinaria;
                    $chartData[] = $count;
                }
            }
        }
    } catch (\Exception $e) {
        \Log::error('Error al obtener estadísticas de maquinarias: ' . $e->getMessage());
        $mostRentedMachinery = [];
        $chartLabels = [];
        $chartData = [];
        session()->flash('error', 'Hubo un error al procesar los datos de maquinarias más alquiladas. Inténtalo de nuevo.');
        session()->forget(['alquileres_ficticios', 'fechas_rango_maquinas']);
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
    $periodType = $request->input('period_type', 'month');

    $fechaInicioStr = $request->input('fecha_inicio');
    $fechaFinStr = $request->input('fecha_fin');
    
    if (!$fechaInicioStr) {
        $fechaInicioStr = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
    }
    if (!$fechaFinStr) {
        $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    try {
        $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
        $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

        if ($fechaInicio->greaterThan($fechaFin)) {
            return redirect()->route('admin.estadisticas.ingresos')
            ->with('error', 'La fecha de inicio no puede ser posterior a la fecha de fin.');
        } 
        
        $fechasGuardadas = session('fechas_rango_ingresos');
        $fechasHanCambiado = false;

        if (!$fechasGuardadas || 
            $fechasGuardadas['inicio'] !== $fechaInicio->timestamp || 
            $fechasGuardadas['fin'] !== $fechaFin->timestamp) 
        {
            $fechasHanCambiado = true;
            session()->forget('pagos_ficticios');
        }

        if ($fechasHanCambiado) {
            $faker = Faker::create('es_ES');
            $numPagos = rand(50, 200);
            $pagosFicticios = [];
            for ($i = 0; $i < $numPagos; $i++) {
                $pagoDate = Carbon::createFromTimestamp(
                    $faker->numberBetween($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                $monto = $faker->randomFloat(2, 10, 500);
                $pagosFicticios[] = (object)['fecha_pago' => $pagoDate, 'monto' => $monto];
            }

            $noIncomeStart = Carbon::parse('2025-01-20')->startOfDay();
            $noIncomeEnd = Carbon::parse('2025-02-20')->endOfDay();

            $pagosFicticios = array_filter($pagosFicticios, function ($pago) use ($noIncomeStart, $noIncomeEnd) {
                return !$pago->fecha_pago->between($noIncomeStart, $noIncomeEnd);
            });
            
            session([
                'pagos_ficticios' => $pagosFicticios,
                'fechas_rango_ingresos' => ['inicio' => $fechaInicio->timestamp, 'fin' => $fechaFin->timestamp]
            ]);

        } else {
            // --- CORRECCIÓN FINAL: Si no se encuentra el dato, usamos un array vacío ---
            $pagosFicticios = session('pagos_ficticios', []);
        }
        
        $totalIncome = array_sum(array_column($pagosFicticios, 'monto'));
        $groupedIncome = [];
        $chartLabels = [];

        if ($periodType === 'week') {
            $period = CarbonPeriod::create($fechaInicio->startOfWeek(), '1 week', $fechaFin->endOfWeek());
            foreach ($period as $date) {
                $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M');
                $chartLabels[] = $weekLabel;
                $groupedIncome[$weekLabel] = 0;
            }
            foreach ($pagosFicticios as $pago) {
                $date = Carbon::parse($pago->fecha_pago);
                $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M');
                if (isset($groupedIncome[$weekLabel])) {
                    $groupedIncome[$weekLabel] += $pago->monto;
                }
            }
        } else {
            $period = CarbonPeriod::create($fechaInicio->startOfMonth(), '1 month', $fechaFin->endOfMonth());
            foreach ($period as $date) {
                $monthYear = $date->format('M Y');
                $chartLabels[] = $monthYear;
                $groupedIncome[$monthYear] = 0;
            }
            foreach ($pagosFicticios as $pago) {
                $monthYear = Carbon::parse($pago->fecha_pago)->format('M Y');
                if (isset($groupedIncome[$monthYear])) {
                    $groupedIncome[$monthYear] += $pago->monto; 
                }
            }
        }
        $chartData = array_values($groupedIncome);

        return view('estadisticas.income', compact('layout', 'totalIncome', 'chartLabels', 'chartData', 'periodType'));

    } catch (\Exception $e) {
        \Log::error('Error al obtener estadísticas de ingresos: ' . $e->getMessage());
        session()->flash('error', 'Hubo un error al procesar los datos de ingresos. Inténtalo de nuevo.');
        
        return view('estadisticas.income', compact('layout', 'totalIncome', 'chartLabels', 'chartData', 'periodType'));
    }
}

    //DESCARGAR PDF

    public function downloadNewClientsStatisticsPdf(Request $request)
{
    $fechaInicioStr = $request->input('fecha_inicio');
    $fechaFinStr = $request->input('fecha_fin');
    $periodType = $request->input('period_type', 'month');

    if (!$fechaInicioStr) {
        $fechaInicioStr = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
    }
    if (!$fechaFinStr) {
        $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    try {
        $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
        $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

        if ($fechaInicio->greaterThan($fechaFin)) {
             return redirect()->route('admin.estadisticas.nuevos-clientes')->with('error', 'La fecha de inicio no puede ser posterior a la fecha de fin para generar el PDF.');
        }

        // --- INICIO: Lógica para usar datos de la sesión ---
        if (session()->has('fechas_clientes')) {
            $fechasFicticias = session('fechas_clientes');
        } else {
            // Si el usuario llega directamente al PDF sin visitar la página de estadísticas,
            // generamos datos nuevos como fallback
            $nuevosClientesCount = rand(50, 500);
            $fechasFicticias = [];
            for ($i = 0; $i < $nuevosClientesCount; $i++) {
                $fechasFicticias[] = Carbon::createFromTimestamp(
                    rand($fechaInicio->timestamp, $fechaFin->timestamp)
                );
            }
            $noClientsStart = Carbon::parse('2025-01-20')->startOfDay();
            $noClientsEnd = Carbon::parse('2025-02-20')->endOfDay();
            $fechasFicticias = array_filter($fechasFicticias, function ($fecha) use ($noClientsStart, $noClientsEnd) {
                return !$fecha->between($noClientsStart, $noClientsEnd);
            });
            session(['fechas_clientes' => $fechasFicticias]);
        }
        // --- FIN: Lógica para usar datos de la sesión ---

        $nuevosClientesCount = count($fechasFicticias);

        if ($nuevosClientesCount === 0) {
            return redirect()->route('admin.estadisticas.nuevos-clientes')->with('error', 'No hay datos para el período seleccionado.');
        }

        $groupedCounts = [];
        $chartLabels = [];

        // El resto del código es el mismo, solo que ahora usa $fechasFicticias
        if ($periodType === 'week') {
            $period = CarbonPeriod::create($fechaInicio->startOfWeek(), '1 week', $fechaFin->endOfWeek());
            foreach ($period as $date) {
                $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M');
                $chartLabels[] = $weekLabel;
                $groupedCounts[$weekLabel] = 0;
            }
            foreach ($fechasFicticias as $fecha) {
                $date = Carbon::parse($fecha);
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
            foreach ($fechasFicticias as $fecha) {
                $monthYear = Carbon::parse($fecha)->format('M Y');
                if (isset($groupedCounts[$monthYear])) {
                    $groupedCounts[$monthYear]++;
                }
            }
        }

        $chartData = array_values($groupedCounts);

        $data = [
            'nuevosClientesCount' => $nuevosClientesCount,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'periodType' => $periodType,
            'fechaInicio' => $fechaInicio->format('d/m/Y'),
            'fechaFin' => $fechaFin->format('d/m/Y'),
        ];

        $pdf = PDF::loadView('estadisticas.new-clients-pdf', $data);

        // Opcional: Eliminar los datos de la sesión después de generar el PDF para limpiar
        session()->forget('fechas_clientes');

        return $pdf->download('estadisticas-nuevos-clientes.pdf');

    } catch (\Exception $e) {
        \Log::error('Error al generar PDF de estadísticas de nuevos clientes: ' . $e->getMessage());
        return redirect()->route('admin.estadisticas.nuevos-clientes')->with('error', 'Hubo un error al generar el PDF de estadísticas. Inténtalo de nuevo.');
    }
}
    public function downloadMostRentedMachineryStatisticsPdf(Request $request)
{
    $fechaInicioStr = $request->input('fecha_inicio');
    $fechaFinStr = $request->input('fecha_fin');

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
            return redirect()->route('admin.estadisticas.maquinas-mas-alquiladas')->with('error', 'La fecha de inicio no puede ser posterior a la fecha de fin para generar el PDF.');
        }

        // --- INICIO: LÓGICA DE DATOS FICTICIOS OBTENIDOS DE LA SESIÓN ---
        $alquileresFicticios = session('alquileres_ficticios', null);

        // Si los datos no están en la sesión, los generamos
        if (is_null($alquileresFicticios)) {
            $faker = Faker::create();
            
            $maquinarias = Maquinaria::all(['id_maquinaria', 'marca', 'modelo']);
            $idsMaquinarias = $maquinarias->pluck('id_maquinaria')->toArray();
            
            $alquileresFicticios = [];
            $numAlquileres = rand(500, 2000);
            
            for ($i = 0; $i < $numAlquileres; $i++) {
                $fechaAlquiler = Carbon::createFromTimestamp(
                    $faker->numberBetween($fechaInicio->timestamp, $fechaFin->timestamp)
                );
                $maquinariaId = $faker->randomElement($idsMaquinarias);
                $alquileresFicticios[] = (object)['id_maquinaria' => $maquinariaId, 'fecha_inicio' => $fechaAlquiler];
            }

            // Caso de fechas sin alquileres (20/01/2025 al 20/02/2025)
            $noRentalsStart = Carbon::parse('2025-01-20')->startOfDay();
            $noRentalsEnd = Carbon::parse('2025-02-20')->endOfDay();

            $alquileresFicticios = array_filter($alquileresFicticios, function ($alquiler) use ($noRentalsStart, $noRentalsEnd) {
                return !$alquiler->fecha_inicio->between($noRentalsStart, $noRentalsEnd);
            });
            
            // Guardamos los nuevos datos en la sesión para futura consistencia
            session([
                'alquileres_ficticios' => $alquileresFicticios,
                'fechas_rango_maquinas' => ['inicio' => $fechaInicio->timestamp, 'fin' => $fechaFin->timestamp]
            ]);
        }
        // --- FIN: LÓGICA DE DATOS FICTICIOS ---

        // Procesamos los alquileres ficticios para obtener las estadísticas
        $conteoAlquileres = [];
        foreach ($alquileresFicticios as $alquiler) {
            // Verificamos que la fecha del alquiler esté dentro del rango seleccionado
            if (Carbon::parse($alquiler->fecha_inicio)->between($fechaInicio, $fechaFin)) {
                $id = $alquiler->id_maquinaria;
                $conteoAlquileres[$id] = ($conteoAlquileres[$id] ?? 0) + 1;
            }
        }
        
        // ** Validar si no hay datos después del procesamiento **
        if (empty($conteoAlquileres)) {
            return redirect()->route('admin.estadisticas.maquinas-mas-alquiladas')->with('error', 'No hay datos de maquinarias alquiladas en el período seleccionado para generar el PDF.');
        }

        // Ordenamos por cantidad y tomamos los top 10
        arsort($conteoAlquileres);
        $top10Ids = array_slice($conteoAlquileres, 0, 10, true);

        // Recuperamos los nombres reales de la DB para los top 10 IDs
        $maquinariasTop10 = Maquinaria::whereIn('id_maquinaria', array_keys($top10Ids))->get()->keyBy('id_maquinaria');
        
        $mostRentedMachinery = [];
        $chartLabels = [];
        $chartData = [];
        foreach ($top10Ids as $id_maquinaria => $count) {
            if (isset($maquinariasTop10[$id_maquinaria])) {
                $maquinaria = $maquinariasTop10[$id_maquinaria];
                $nombreMaquinaria = $maquinaria->marca . ' ' . $maquinaria->modelo; 
                
                $mostRentedMachinery[] = [
                    'nombre' => $nombreMaquinaria,
                    'cantidad_alquileres' => $count,
                ];
                $chartLabels[] = $nombreMaquinaria;
                $chartData[] = $count;
            }
        }

        // Preparar datos para la vista del PDF
        $data = [
            'mostRentedMachinery' => $mostRentedMachinery,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'fechaInicio' => $fechaInicio->format('d/m/Y'),
            'fechaFin' => $fechaFin->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('estadisticas.most-rented-machinery-pdf', $data);

        return $pdf->download('estadisticas-maquinas-mas-alquiladas.pdf');

    } catch (\Exception $e) {
        \Log::error('Error al generar PDF de estadísticas de maquinarias más alquiladas: ' . $e->getMessage());
        return redirect()->route('admin.estadisticas.maquinas-mas-alquiladas')->with('error', 'Hubo un error al generar el PDF de estadísticas. Inténtalo de nuevo.');
    }
}
    public function downloadIncomeStatisticsPdf(Request $request)
    {
        $fechaInicioStr = $request->input('fecha_inicio');
        $fechaFinStr = $request->input('fecha_fin');
        $periodType = $request->input('period_type', 'month');

        if (!$fechaInicioStr) {
            $fechaInicioStr = Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
        }
        if (!$fechaFinStr) {
            $fechaFinStr = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        try {
            $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
            $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

            if ($fechaInicio->greaterThan($fechaFin)) {
                return redirect()->route('admin.estadisticas.ingresos')->with('error', 'La fecha de inicio no puede ser posterior a la fecha de fin para generar el PDF.');
            }

            // --- INICIO: Lógica para usar datos de la sesión ---
            if (session()->has('pagos_ficticios')) {
                $pagosFicticios = session('pagos_ficticios');
            } else {
                // Fallback: si el usuario no ha visitado la página de estadísticas,
                // generamos datos nuevos.
                $faker = Faker::create('es_ES');
                $numPagos = rand(50, 200);
                $pagosFicticios = [];

                for ($i = 0; $i < $numPagos; $i++) {
                    $pagoDate = Carbon::createFromTimestamp(
                        $faker->numberBetween($fechaInicio->timestamp, $fechaFin->timestamp)
                    );
                    $monto = $faker->randomFloat(2, 10, 500);
                    $pagosFicticios[] = (object)['fecha_pago' => $pagoDate, 'monto' => $monto];
                }

                // Aplicamos el filtro para el caso de fechas sin ingresos
                $noIncomeStart = Carbon::parse('2025-01-20')->startOfDay();
                $noIncomeEnd = Carbon::parse('2025-02-20')->endOfDay();
                $pagosFicticios = array_filter($pagosFicticios, function ($pago) use ($noIncomeStart, $noIncomeEnd) {
                    return !$pago->fecha_pago->between($noIncomeStart, $noIncomeEnd);
                });

                // Opcional: guardamos los datos generados para futuras descargas
                session(['pagos_ficticios' => $pagosFicticios, 'fechas_rango_ingresos' => ['inicio' => $fechaInicio->timestamp, 'fin' => $fechaFin->timestamp]]);
            }
            // --- FIN: Lógica para usar datos de la sesión ---

            // Calculamos el ingreso total a partir de los datos ficticios
            $totalIncome = array_sum(array_column($pagosFicticios, 'monto'));

            // Validar si el ingreso total es cero antes de generar el PDF
            if ($totalIncome === 0) {
                return redirect()->route('admin.estadisticas.ingresos')->with('error', 'No hay ingresos registrados en el período seleccionado para generar el PDF.');
            }

            $groupedIncome = [];
            $chartLabels = [];

            if ($periodType === 'week') {
                $period = CarbonPeriod::create($fechaInicio->startOfWeek(), '1 week', $fechaFin->endOfWeek());
                foreach ($period as $date) {
                    $weekLabel = 'Semana ' . $date->weekOfYear . ' de ' . $date->startOfWeek()->translatedFormat('M');
                    $chartLabels[] = $weekLabel;
                    $groupedIncome[$weekLabel] = 0;
                }
                foreach ($pagosFicticios as $pago) {
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
                foreach ($pagosFicticios as $pago) {
                    $monthYear = Carbon::parse($pago->fecha_pago)->format('M Y');
                    if (isset($groupedIncome[$monthYear])) {
                        $groupedIncome[$monthYear] += $pago->monto; 
                    }
                }
            }
            $chartData = array_values($groupedIncome);

            $data = [
                'totalIncome' => $totalIncome,
                'chartLabels' => $chartLabels,
                'chartData' => $chartData,
                'periodType' => $periodType,
                'fechaInicio' => $fechaInicio->format('d/m/Y'),
                'fechaFin' => $fechaFin->format('d/m/Y'),
            ];

            $pdf = Pdf::loadView('estadisticas.income-pdf', $data);

            // Opcional: limpiar los datos de la sesión después de generar el PDF
            session()->forget('pagos_ficticios');

            return $pdf->download('estadisticas-ingresos.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al generar PDF de estadísticas de ingresos: ' . $e->getMessage());
            return redirect()->route('admin.estadisticas.ingresos')->with('error', 'Hubo un error al generar el PDF de estadísticas. Inténtalo de nuevo.');
        }
    }
}

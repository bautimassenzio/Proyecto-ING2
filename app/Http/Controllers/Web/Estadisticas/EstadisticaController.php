<?php

namespace App\Http\Controllers\Web\Estadisticas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Domain\User\Models\Usuario;
use App\Enums\Roles;

class EstadisticaController extends Controller
{
    /**
     * Muestra la vista principal de estadísticas para los administradores.
     * En el futuro, aquí se recopilarán todos los datos necesarios para las estadísticas.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showStatistics(Request $request)
    {
         // Define el layout a usar. Asumimos 'layouts.admin' para administradores.
        $layout = session('layout', 'layouts.admin');

        $nuevosClientesCount = null; // Inicializamos a null para el primer acceso a la vista

        // Recoge las fechas del request. Si no se proporcionan, usa valores por defecto.
        $fechaInicioStr = $request->input('fecha_inicio');
        $fechaFinStr = $request->input('fecha_fin');

        if ($fechaInicioStr && $fechaFinStr) {
            try {
                // Parsea las fechas a objetos Carbon para facilitar las comparaciones
                $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
                $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

                // Regla de Negocio 1: Contar nuevos clientes en el rango de fechas
                // Asegúrate de que 'rol' y 'fecha_alta' sean los nombres correctos de tus campos.
                $nuevosClientesCount = Usuario::where('rol', Roles::CLIENTE->value) // Asumiendo que Roles::CLIENTE->value devuelve 'cliente'
                                          ->whereBetween('fecha_alta', [$fechaInicio, $fechaFin])
                                          ->count();

            } catch (\Exception $e) {
                // Manejo de error si las fechas no son válidas o hay un problema en la consulta
                // Puedes loggear el error o pasar un mensaje a la vista
                \Log::error('Error al obtener estadísticas de clientes: ' . $e->getMessage());
                $nuevosClientesCount = 0; // O un valor que indique error
                session()->flash('error', 'Hubo un error al procesar las fechas. Inténtalo de nuevo.');
            }
        }
        
        // Pasa las variables a la vista
        return view('estadisticas.estadistica', compact('layout', 'nuevosClientesCount'));
    }

    // Aquí podrás añadir otros métodos para diferentes tipos de estadísticas si es necesario
    // Por ejemplo:
    // public function getReservationsData(Request $request) { /* ... */ }
    // public function getMachineryUsageData(Request $request) { /* ... */ }
}
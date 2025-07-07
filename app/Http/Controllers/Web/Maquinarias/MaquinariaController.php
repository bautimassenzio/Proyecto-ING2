<?php
namespace App\Http\Controllers\Web\Maquinarias;

use App\Http\Controllers\Controller;
use App\Domain\Maquinaria\Models\Maquinaria;
use App\Domain\Maquinaria\Models\Politica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Domain\Reserva\Models\Reserva;
use Carbon\Carbon;

class MaquinariaController extends Controller
{
     public function __construct()
    {   
       // $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
        // USAMOS 'admin' aquí, como en tu Enum
        $this->middleware('checkUserType:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('checkUserType:empleado')->only(['devolucionesPendientes']);
    }

    public function index()
    {
        // También usamos 'admin' aquí para consistencia
        if (Auth::check() && Auth::user()->rol === 'admin') { // <-- Cambiado de 'administrador' a 'admin'
            $maquinarias = Maquinaria::all();
        } else {
            $maquinarias = Maquinaria::where('estado', 'disponible')->get();
        }
        $usuario = Auth::check() ? Auth::user() : null;
        $layout = session('layout', 'layouts.base');
        return view('maquinarias.index', compact('maquinarias', 'usuario', 'layout'));
    }

    public function show($id_maquinaria)
    {
        // También usamos 'admin' aquí para consistencia
        if (Auth::check() && Auth::user()->rol === 'admin') { // <-- Cambiado de 'administrador' a 'admin'
            $maquinaria = Maquinaria::findOrFail($id_maquinaria);
        } else {
            $maquinaria = Maquinaria::where('id_maquinaria', $id_maquinaria)
                                     ->where('estado', 'disponible')
                                     ->firstOrFail();
        }
        return view('maquinarias.createMaq', compact('maquinaria'));
    }


    // Crear maquinaria (solo admin)
    public function create()
    {
        $politicas = Politica::all();
        return view('maquinarias.createMaq', compact('politicas'));
    }

    // Guardar maquinaria (solo admin)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nro_inventario' => 'required|string|max:255|unique:maquinarias',
            'precio_dia' => 'required|numeric|min:0',
            'foto_url' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'required|integer|min:1900|max:' . date('Y'),
            'uso' => 'required|string|max:100',
            'tipo_energia' => 'required|string|in:electrica,combustion',
            'estado' => 'required|string|in:disponible,inactiva',
            'localidad' => 'required|string|max:100',
            'id_politica' => 'required|integer|exists:politicas,id_politica',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('foto_url')) {
            $imagePath = $request->file('foto_url')->store('maquinarias', 'public');
            $validatedData['foto_url'] = $imagePath;
        } else {
            $validatedData['foto_url'] = null;
        }

        Maquinaria::create($validatedData);

        return redirect()->route('catalogo.index')->with('success', 'Maquinaria creada exitosamente.');
    }

    // Dar de baja maquinaria (solo admin)
    public function destroy(Maquinaria $maquinaria)
    {
        // --- INICIO: REGISTRO DE DEPURACIÓN EN EL CONTROLADOR ---
        Log::info('Destroy method called for maquinaria ID: ' . $maquinaria->id_maquinaria);
        // --- FIN: REGISTRO DE DEPURACIÓN ---

        try {
            $deleted = $maquinaria->delete(); // Esta llamada debería disparar el evento 'deleting'

            if ($deleted) {
                Log::info('Maquinaria ID ' . $maquinaria->id_maquinaria . ' successfully soft-deleted.');
                return redirect()->route('catalogo.index')->with('success', 'Maquinaria dada de baja exitosamente.');
            } else {
                Log::warning('Maquinaria ID ' . $maquinaria->id_maquinaria . ' deletion returned false (likely due to active reservations).');
                return redirect()->route('catalogo.index')->with('error', 'No se puede dar de baja la maquinaria porque tiene reservas activas.');
            }
        } catch (QueryException $e) {
            Log::error('QueryException caught during Maquinaria deletion for ID ' . $maquinaria->id_maquinaria . ': ' . $e->getMessage());
            if ($e->getCode() === '23000' && str_contains($e->getMessage(), '1451')) {
                return redirect()->route('catalogo.index')->with('error', 'ERROR: La maquinaria no pudo ser dada de baja. Aún tiene reservas activas o relacionadas en la base de datos.');
            }
            throw $e; // Vuelve a lanzar la excepción si no es una violación de FK específica.
        } catch (\Exception $e) {
            Log::error('Unexpected exception caught during Maquinaria deletion for ID ' . $maquinaria->id_maquinaria . ': ' . $e->getMessage());
            return redirect()->route('catalogo.index')->with('error', 'Ocurrió un error inesperado al intentar dar de baja la maquinaria: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar una maquinaria existente.
     */
    public function edit(Maquinaria $maquinaria)
    {
        // El Route Model Binding (Maquinaria $maquinaria) ya busca la maquinaria
        // por su ID y la inyecta directamente. Si no la encuentra, Laravel arroja un 404.
        $politicas = Politica::all(); // Si necesitas políticas para un select en el form

        return view('Maquinarias.edit', compact('maquinaria', 'politicas'));
    }

    /**
     * Actualiza una maquinaria existente en la base de datos.
     */
    public function update(Request $request, Maquinaria $maquinaria)
    {
        $request->validate([
            'precio_dia' => 'required|numeric|min:1',
            'estado' => 'required|string|in:disponible,inactiva',       // Ajusta si es un ENUM como 'disponible,inactiva'
            ]);

        // Manejo de la subida de la nueva foto
        $data = $request->except('foto_url'); // Obtiene todos los datos EXCEPTO la foto

        if ($request->hasFile('foto_url')) {
            // Si hay una foto existente, la eliminamos primero
            if ($maquinaria->foto_url) {
                Storage::delete($maquinaria->foto_url);
            }
            // Almacena la nueva foto
            $path = $request->file('foto_url')->store('public/maquinarias_fotos');
            $data['foto_url'] = $path; // Guarda la nueva ruta en los datos
        }

        // Actualiza la maquinaria con los datos validados
        $maquinaria->update($data);

        return redirect()->route('catalogo.index')->with('success', 'Maquinaria actualizada exitosamente.');
    }

    public function devolucionesPendientes()
    {
        $maquinariasConDevolucionPendiente = Maquinaria::whereHas('reservas', function ($query) {
            $query->where(function ($q) {
                // Condición 1: Reservas aprobadas y vencidas
                $q->where('estado', 'aprobada')
                  ->whereDate('fecha_fin', '<=', Carbon::today());
            })->orWhere(function ($q) {
                // Condición 2: Todas las reservas activas (entregadas), sin importar la fecha de fin
                $q->where('estado', 'aprobada');
            })->orWhere(function ($q) {
                // Condición 3: Todas las reservas finalizadas (para registro)
                $q->where('estado', 'finalizada'); // <-- ¡NUEVA CONDICIÓN AQUÍ!
            });
        })
        ->with(['reservas' => function ($query) {
            // Cargamos solo las reservas que cumplen el criterio para mostrarlas
            $query->where(function ($q) {
                $q->where('estado', 'aprobada')
                  ->whereDate('fecha_fin', '<=', Carbon::today());
            })->orWhere(function ($q) {
                $q->where('estado', 'aprobada');
            })->orWhere(function ($q) {
                $q->where('estado', 'finalizada'); // <-- ¡NUEVA CONDICIÓN AQUÍ!
            })
            ->orderBy('fecha_fin', 'asc'); // Mantenemos el orden por fecha de fin
        }, 'reservas.cliente']) // Asegúrate de cargar también la relación 'cliente' en las reservas
        ->get();

        return view('maquinarias.devoluciones-pendientes', compact('maquinariasConDevolucionPendiente'));
    }
}

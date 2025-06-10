<?php
namespace App\Http\Controllers\Web\Maquinarias;

use App\Http\Controllers\Controller;
use App\Domain\Maquinaria\Models\Maquinaria;
use App\Domain\Maquinaria\Models\Politica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaquinariaController extends Controller
{
     public function __construct()
    {   
       // $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
        // USAMOS 'admin' aquí, como en tu Enum
        $this->middleware('checkUserType:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
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
        $layout = session('layout', 'layouts.visitante');
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
        $layout=session('layout');
        return view('maquinarias.createMaq', compact('politicas','layout'));
    }

    // Guardar maquinaria (solo admin)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nro_inventario' => 'required|string|max:255|unique:maquinarias',
            'precio_dia' => 'required|numeric|min:0',
            'foto_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'required|integer|min:1900|max:' . date('Y'),
            'uso' => 'required|string|max:100',
            'tipo_energia' => 'required|string|in:electrica,combustion',
            'estado' => 'required|string|in:disponible,inactiva',
            'localidad' => 'required|string|max:100',
            'id_politica' => 'required|integer|exists:politicas,id_politica',
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
        $maquinaria->estado = 'inactiva';
        $maquinaria->save();
        $maquinarias = Maquinaria::all(); // O Maquinaria::where('estado', 'disponible')->get(); etc.

        // 3. Obtener el usuario autenticado (si lo necesitas para la vista)
        $usuario = Auth::guard('users')->user(); // Asumiendo que tu guard es 'users'

        // 4. Obtener el layout de la sesión (si lo usas para la vista)
        $layout = session('layout');


        // 5. Redirigir a la vista de índice con los datos necesarios
        //    Es más común y mejor práctica REDIRIGIR a la ruta del índice
        //    en lugar de devolver directamente la vista después de una acción POST/DELETE.
        //    Esto evita problemas como reenviar el formulario al recargar la página.
        return redirect()->route('catalogo.index')->with('success', 'Maquinaria dada de baja exitosamente.');

    }
}

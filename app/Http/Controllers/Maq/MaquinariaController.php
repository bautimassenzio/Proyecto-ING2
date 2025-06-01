<?php

namespace App\Http\Controllers\Maq;

use App\Http\Controllers\Controller;
use App\Domain\Maquinaria\Maquinaria; // O App\Domain\Maquinaria;
use App\Domain\Maquinaria\Politica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Para manejar la subida de imágenes

class MaquinariaController extends Controller
{   
    public function __construct()
    {
        //$this->middleware('auth');Esta es la buena
        $this->middleware('auth')->except(['index', 'show']);//Temporal para poder ver sin autenticar
        $this->middleware('checkUserType:administrador')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Muestra los detalles de una maquinaria específica, adaptándose al rol.
     * Si no quieres que los administradores vean los detalles de las dadas de baja
     * por medio de este 'show', también deberías ajustar esta lógica.
     */
    public function show($id_maquinaria)
    {
        $maquinaria = null;

        if (Auth::check() && Auth::user()->role === 'administrador') {
            // Si es administrador, buscamos la maquinaria.
            // Para que el administrador tampoco vea los detalles de las dadas de baja,
            // quitamos 'withTrashed()' aquí también.
            $maquinaria = Maquinaria::findOrFail($id_maquinaria); // <-- Quitamos withTrashed()
        } else {
            // Para usuarios normales, solo buscamos maquinarias disponibles y activas
            $maquinaria = Maquinaria::where('id_maquinaria', $id_maquinaria)
                                     ->where('estado', 'disponible')
                                     ->firstOrFail();
        }

        // Si la maquinaria está soft-deleted y el usuario no es admin o no debería verla,
        // esto manejará el caso. Si se encuentra un 'trashed' y no es admin,
        // el 'firstOrFail' en el else lo maneja. Si es admin, y el 'findOrFail'
        // encuentra un 'trashed', aún la mostrará. Para evitarlo para admins también:
        if ($maquinaria->trashed()) {
            abort(404, 'Maquinaria no disponible o no encontrada.');
        }

        return view('admin.maquinarias.show', compact('maquinaria'));
    }


     public function create()
    {
        $politicas = Politica::all(); // Obtiene todas las políticas
        return view('admin.maquinarias.create', compact('politicas'));
    }

    /**
     * Almacena una nueva maquinaria en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos
        $validatedData = $request->validate([
            'nro_inventario' => 'required|string|max:255|unique:maquinarias',
            'precio_dia' => 'required|numeric|min:0',
            'foto_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Para imágenes
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'required|integer|min:1900|max:' . date('Y'),
            'uso'=> 'required|string|max:100',
            'tipo_energia' => 'required|string|in:electrica,combustion',
            'estado' => 'required|string|in:disponible,inactiva',
            'localidad' => 'required|string|max:100',
            'id_politica' => 'nullable|integer|exists:politicas,id_politica',
        ]);

        // 2. Manejo de la subida de la imagen
        if ($request->hasFile('foto_url')) {
            // Guarda la imagen en storage/app/public/maquinarias
            // $imagePath contendrá algo como 'maquinarias/nombre_aleatorio.jpg'
            $imagePath = $request->file('foto_url')->store('maquinarias', 'public');
            $validatedData['foto_url'] = $imagePath; // Guarda esta ruta relativa a 'storage/app/public'
        } else {
            $validatedData['foto_url'] = null; // Asegura que sea null si no se sube imagen
        }


        // 3. Crear la maquinaria
        $maquinaria =Maquinaria::create($validatedData);

        // 4. Redireccionar con un mensaje de éxito
        return redirect()->route('maquinarias.index')->with('success', 'Maquinaria creada exitosamente.');
    }
    // ... otros métodos (index, show, edit, update, destroy) ...
    public function index()
    {
        $maquinarias = Maquinaria::all(); // Obtiene todas las maquinarias
        return view('admin.maquinarias.index', compact('maquinarias'));
    }
    public function destroy(Maquinaria $maquinaria) // Route Model Binding para obtener la maquinaria
    {
        // Esto marca la maquinaria como eliminada (establece la columna 'deleted_at')
        $maquinaria->delete();

        // Puedes redirigir al listado con un mensaje de éxito
        return redirect()->route('maquinarias.index')->with('success', 'Maquinaria dada de baja exitosamente.');
    }
}
<?php

namespace App\Http\Controllers\Maq;

use App\Http\Controllers\Controller;
use App\Domain\Maquinaria\Maquinaria; // O App\Domain\Maquinaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Para manejar la subida de imágenes

class MaquinariaController extends Controller
{
    /**
     * Muestra una lista de todas las maquinarias.
     */
    public function index()
    {
        $maquinarias = Maquinaria::all();
        return view('admin.maquinarias.index', compact('maquinarias'));
    }

    /**
     * Muestra el formulario para crear una nueva maquinaria.
     */
    public function create()
    {
        return view('admin.maquinarias.create');
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
            'year_manufacture' => 'nullable|integer|min:1900|max:' . date('Y'),
            'features' => 'nullable|json', // Asegúrate que el frontend envía JSON si es un campo complejo
            'status' => 'required|string|in:available,sold,rented',
        ]);

        // 2. Manejo de la subida de la imagen
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/maquinarias'); // Guarda en storage/app/public/maquinarias
            $validatedData['image'] = Storage::url($imagePath); // Obtiene la URL pública
        }

        // 3. Crear la maquinaria
        Maquinaria::create($validatedData);
        //temporal
        //dd('Maquinaria guardada, a punto de redirigir');
        // 4. Redireccionar con un mensaje de éxito
        return redirect()->route('maquinarias.index')->with('success', 'Maquinaria creada exitosamente.');
    }

    // Puedes agregar métodos edit, update, destroy si los necesitas
}
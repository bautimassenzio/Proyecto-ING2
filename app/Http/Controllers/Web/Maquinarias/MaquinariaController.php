<?php
namespace App\Http\Controllers\Web\Maquinarias;

use App\Http\Controllers\Controller;
use App\Domain\Maquinaria\Models\Maquinaria;
use App\Domain\Maquinaria\Models\Politica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


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
            'precio_dia' => 'required|numeric|min:1',
            'foto_url' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'required|integer|min:1900|max:' . date('Y'),
            'uso' => 'required|string|max:100',
            'tipo_energia' => 'required|string|in:electrica,combustion',
            'estado' => 'required|string|in:disponible,inactiva',
            'localidad' => 'required|string|max:100',
            'id_politica' => 'required|integer|exists:politicas,id_politica',
            'descripcion' => 'required|string|max:1000',
        ], [
            'nro_inventario.required' => 'El número de inventario es obligatorio.',
            'nro_inventario.unique' => 'Ya existe una maquinaria con ese número de inventario.',
            'precio_dia.required' => 'El precio por día es obligatorio.',
            'precio_dia.min'=>'El precio debe ser mayor a 0',
            'foto_url.required' => 'Debe subir una imagen de la maquinaria.',
            'foto_url.image' => 'El archivo debe ser una imagen.',
            'foto_url.mimes' => 'La imagen debe ser de tipo jpeg, png o jpg',
            'foto_url.max'=>'La foto no puede superar los 5MB',
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required' => 'El modelo es obligatorio.',
            'anio.required' => 'El año es obligatorio.',
            'anio.integer' => 'El año debe ser un número.',
            'anio.min' => 'El año no puede ser menor a 1900.',
            'anio.max' => 'El año no puede ser mayor al actual.',
            'uso.required' => 'El uso es obligatorio.',
            'tipo_energia.required' => 'Debe especificar el tipo de energía.',
            'tipo_energia.in' => 'El tipo de energía debe ser eléctrica o combustión.',
            'estado.required' => 'Debe especificar el estado de la maquinaria.',
            'estado.in' => 'El estado debe ser disponible o inactiva.',
            'localidad.required' => 'La localidad es obligatoria.',
            'id_politica.required' => 'Debe seleccionar una política.',
            'id_politica.exists' => 'La política seleccionada no existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
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
    // Verificar si tiene reservas asociadas
    if ($maquinaria->reserva()->exists()) {
        return redirect()->route('catalogo.index')
            ->with('error', 'No se puede eliminar la maquinaria porque tiene reservas asociadas.');
    }

    // Eliminar la maquinaria completamente de la base de datos
    $maquinaria->delete();

    return redirect()->route('catalogo.index')
        ->with('success', 'Maquinaria eliminada exitosamente.');
}


    /**
     * Muestra el formulario para editar una maquinaria existente.
     */
    public function edit(Maquinaria $maquinaria)
    {
        // El Route Model Binding (Maquinaria $maquinaria) ya busca la maquinaria
        // por su ID y la inyecta directamente. Si no la encuentra, Laravel arroja un 404.
        $politicas = Politica::all(); // Si necesitas políticas para un select en el form
        $layout=session('layout','layouts.base');
        return view('Maquinarias.edit', compact('maquinaria', 'politicas','layout'));
    }

    /**
     * Actualiza una maquinaria existente en la base de datos.
     */
    public function update(Request $request, Maquinaria $maquinaria)
    {

        $request->validate([
                'nro_inventario' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('maquinarias', 'nro_inventario')->ignore($maquinaria->id_maquinaria, 'id_maquinaria'),
                ],
                'precio_dia' => 'sometimes|required|numeric|min:1',
                'marca' => 'sometimes|required|string|max:255',
                'modelo' => 'sometimes|required|string|max:255',
                'localidad' => 'sometimes|required|string|max:255',
                'anio' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
                'uso' => 'sometimes|required|string|max:255',
                'tipo_energia' => 'sometimes|required|string|in:electrica,combustion',
                'estado' => 'sometimes|required|string|in:disponible,inactiva',
                'foto_url' => 'sometimes|required|image|mimes:jpg,jpeg,png|max:5120',
                'id_politica' => 'sometimes|required|exists:politicas,id_politica',
                'descripcion' => 'sometimes|required|string|max:1000',
            ], [
                'nro_inventario.required' => 'El número de inventario es obligatorio.',
                'nro_inventario.unique' => 'Ya existe una maquinaria con ese número de inventario.',
                'nro_inventario.max' => 'El número de inventario no debe exceder los 255 caracteres.',
                'precio_dia.required' => 'El precio por día es obligatorio.',
                'precio_dia.numeric' => 'El precio por día debe ser un número.',
                'precio_dia.min' => 'El precio por día debe ser al menos 1.',
                'marca.required' => 'La marca es obligatoria.',
                'modelo.required' => 'El modelo es obligatorio.',
                'localidad.required' => 'La localidad es obligatoria.',
                'anio.required' => 'El año es obligatorio.',
                'anio.integer' => 'El año debe ser un número entero.',
                'anio.min' => 'El año no puede ser menor a 1900.',
                'anio.max' => 'El año no puede ser mayor a ' . (date('Y') + 1) . '.',
                'uso.required' => 'El uso es obligatorio.',
                'tipo_energia.required' => 'El tipo de energía es obligatorio.',
                'tipo_energia.in' => 'El tipo de energía debe ser "eléctrica" o "combustión".',
                'estado.required' => 'El estado es obligatorio.',
                'estado.in' => 'El estado debe ser "disponible" o "inactiva".',
                'foto_url.required' => 'La imagen es obligatoria.',
                'foto_url.image' => 'El archivo debe ser una imagen válida.',
                'foto_url.mimes' => 'La imagen debe ser de tipo JPG, JPEG o PNG.',
                'foto_url.max' => 'La imagen no debe superar los 5MB.',
                'id_politica.required' => 'Debe seleccionar una política de cancelación.',
                'id_politica.exists' => 'La política seleccionada no existe.',
                'descripcion.required' => 'La descripción es obligatoria.',
                'descripcion.max' => 'La descripción no debe superar los 1000 caracteres.',
            ]);


        // Manejo de la subida de la nueva foto
        $data = $request->except('foto_url'); // Obtiene todos los datos EXCEPTO la foto

        if ($request->hasFile('foto_url')) {
            // Si hay una foto existente, la eliminamos primero
            if ($maquinaria->foto_url) {
                Storage::delete($maquinaria->foto_url);
            }
            // Almacena la nueva foto
            $path = $request->file('foto_url')->store('maquinarias', 'public');
            $data['foto_url'] = $path; // Guarda la nueva ruta en los datos
        }

        // Actualiza la maquinaria con los datos validados
        $maquinaria->update($data);

        return redirect()->route('catalogo.index')->with('success', 'Maquinaria actualizada exitosamente.');
    }
}
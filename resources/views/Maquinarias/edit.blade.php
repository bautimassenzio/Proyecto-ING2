@extends($layout)

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Editar Maquinaria: {{ $maquinaria->nro_inventario }}</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('maquinarias.update', $maquinaria->id_maquinaria) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Precio por Día -->
        <div>
            <label for="precio_dia" class="form-label">Precio por Día</label>
            <input type="number" name="precio_dia" id="precio_dia" step="0.01"
                   value="{{ old('precio_dia', $maquinaria->precio_dia) }}"
                   class="form-control">
          
        </div>

        <!-- Estado -->
        <div>
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select">
                <option value="disponible" {{ old('estado', $maquinaria->estado) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="inactiva" {{ old('estado', $maquinaria->estado) == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
            </select>
          
        </div>

        <!-- Localidad -->
 <div>
    <label for="localidad_id" class="form-label">Localidad</label>
    <select name="localidad_id" id="localidad_id" class="form-control" required>
        <option value="">Seleccione una localidad</option>
        @foreach($localidades as $localidad)
            <option value="{{ $localidad->id }}"
                {{ old('localidad_id', $maquinaria->localidad_id) == $localidad->id ? 'selected' : '' }}>
                {{ $localidad->nombre }}
            </option>
        @endforeach
    </select>
</div>

        <!-- Política de Cancelación -->
        <div>
            <label for="id_politica" class="form-label">Política de Cancelación</label>
            <select name="id_politica" id="id_politica" class="form-select">
                @foreach($politicas as $politica)
                    <option value="{{ $politica->id_politica }}" {{ old('id_politica') == $politica->id_politica ? 'selected' : '' }}>
                        {{ $politica->tipo }}
                        @if($politica->detalle) ({{ $politica->detalle }}) @endif
                    </option>
                @endforeach
            </select>
            
        </div>

        <!-- Foto -->
        <div>
            <label for="foto_url" class="form-label">Foto</label>
            <input type="file" name="foto_url" id="foto_url" class="form-control">
            

            @if ($maquinaria->foto_url)
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Foto actual:</p>
                    <img src="{{ Storage::url($maquinaria->foto_url) }}" alt="Foto actual" style="width: 600px; height: auto;" class="rounded border">

                </div>
            @endif
        </div>

         <div>
            <label for="descripcion" class="form-label">Descripcion</label>
            <input type="text" name="descripcion" id="descripcion"
                   value="{{ old('descripcion', $maquinaria->descripcion) }}"
                   class="form-control">
            
        </div>

        <!-- Botones -->
        <div class="mt-8 flex justify-end space-x-4">
            <button type="submit" class="btn btn-primary">
                Actualizar Maquinaria
            </button>
            <a href="{{ route('catalogo.index') }}" class="px-6 py-3 bg-gray-500 text-white font-semibold rounded-md shadow-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition duration-200">
                Cancelar
            </a>
        </div>
    </form>
@endsection
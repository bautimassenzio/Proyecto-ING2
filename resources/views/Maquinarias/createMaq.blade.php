@extends($layout) {{-- ESTA LÍNEA ES CORRECTA --}}

@section('content') {{-- Y ESTO DEBE CONTENER SOLAMENTE EL CONTENIDO --}}
<div class="container">
    <h1>Alta de Maquinaria</h1>

 

    <form action="{{ route('maquinarias.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="nro_inventario" class="form-label">Número de Inventario:</label>
            <input type="text" class="form-control" id="nro_inventario" name="nro_inventario" value="{{ old('nro_inventario') }}" required>
          
        </div>

        <div class="mb-3">
            <label for="precio_dia" class="form-label">Precio:</label>
            <input type="number" step="0.01" class="form-control" id="precio_dia" name="precio_dia" value="{{ old('precio_dia') }}" required>
           
        </div>

        <div class="mb-3">
            <label for="foto_url" class="form-label">Imagen:</label>
            <input type="file" class="form-control" id="foto_url" name="foto_url">
           
        </div>

        <div class="mb-3">
            <label for="marca" class="form-label">Marca:</label>
            <input type="text" class="form-control" id="marca" name="marca" value="{{ old('marca') }}" required>
          
        </div>

        <div class="mb-3">
            <label for="modelo" class="form-label">Modelo:</label>
            <input type="text" class="form-control" id="modelo" name="modelo" value="{{ old('modelo') }}" required>
          
        </div>
<div class="mb-3">
    <label for="localidad_id" class="form-label">Localidad:</label>
    <select class="form-control" id="localidad_id" name="localidad_id" required>
        <option value="">Seleccione una localidad</option>
        @foreach($localidades as $localidad)
            <option value="{{ $localidad->id }}" {{ old('localidad_id') == $localidad->id ? 'selected' : '' }}>
                {{ $localidad->nombre }}
            </option>
        @endforeach
    </select>
</div>


        <div class="mb-3">
            <label for="anio" class="form-label">Año:</label>
            <input type="number" class="form-control" id="anio" name="anio" value="{{ old('anio') }}" required>
            
        </div>

            <div class="mb-3">
            <label for="tipo_de_uso_id" class="form-label">Tipo de Uso:</label>
            <select class="form-control" id="tipo_de_uso_id" name="tipo_de_uso_id" required>
                <option value="">Seleccione un tipo de uso</option>
                @foreach($tiposDeUso as $tipo)
                    <option value="{{ $tipo->id }}" {{ old('tipo_de_uso_id') == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tipo_energia" class="form-label">Tipo de Energia:</label>
            <select class="form-select" id="tipo_energia" name="tipo_energia" required>
                <option value="electrica" {{ old('tipo_energia') == 'electrica' ? 'selected' : '' }}>Electrica</option>
                <option value="combustion" {{ old('tipo_energia') == 'combustion' ? 'selected' : '' }}>Combustion</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_politica" class="form-label">Política Asociada:</label>
            <select class="form-control" id="id_politica" name="id_politica">
                <option value="">Seleccione una Política</option>
                @foreach($politicas as $politica)
                    <option value="{{ $politica->id_politica }}" {{ old('id_politica') == $politica->id_politica ? 'selected' : '' }}>
                        {{ $politica->tipo }}
                        @if($politica->detalle) ({{ $politica->detalle }}) @endif
                    </option>
                @endforeach
            </select>
           
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado:</label>
            <select class="form-select" id="estado" name="estado" required>
                <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="inactiva" {{ old('estado') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
            </select>
        </div>

         <div class="mb-3">
            <label for="descripcion" class="form-label">Descripcion:</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ old('descripcion') }}" required>
           
        </div>

        {{-- Si usas 'features' como JSON y necesitas un input, podría ser un textarea para JSON o varios inputs dinámicos --}}
        {{-- <div class="mb-3">
            <label for="features" class="form-label">Características (JSON):</label>
            <textarea class="form-control" id="features" name="features">{{ old('features') }}</textarea>
        </div> --}}

        <button type="submit" class="btn btn-primary">Guardar Maquinaria</button>
    </form>
</div>
@endsection
@extends('layouts.base') {{-- ESTA LÍNEA ES CORRECTA --}}

@section('content') {{-- Y ESTO DEBE CONTENER SOLAMENTE EL CONTENIDO --}}
<div class="container">
    <h1>Alta de Maquinaria</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('maquinarias.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="nro_inventario" class="form-label">Número de Inventario:</label>
            <input type="text" class="form-control" id="nro_inventario" name="nro_inventario" value="{{ old('nro_inventario') }}" required>
            @error('nro_inventario')
                    <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="precio_dia" class="form-label">Precio:</label>
            <input type="number" step="0.01" class="form-control" id="precio_dia" name="precio_dia" value="{{ old('precio_dia') }}" required>
            @error('precio_dia')
                    <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="foto_url" class="form-label">Imagen:</label>
            <input type="file" class="form-control" id="foto_url" name="foto_url">
            @error('foto_url')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="marca" class="form-label">Marca:</label>
            <input type="text" class="form-control" id="marca" name="marca" value="{{ old('marca') }}" required>
            @error('marca')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="modelo" class="form-label">Modelo:</label>
            <input type="text" class="form-control" id="modelo" name="modelo" value="{{ old('modelo') }}" required>
            @error('modelo')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="status" class="form-label">Estado:</label>
            <select class="form-select" id="status" name="status" required>
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Vendido</option>
                <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>Alquilado</option>
            </select>
        </div>

        {{-- Si usas 'features' como JSON y necesitas un input, podría ser un textarea para JSON o varios inputs dinámicos --}}
        {{-- <div class="mb-3">
            <label for="features" class="form-label">Características (JSON):</label>
            <textarea class="form-control" id="features" name="features">{{ old('features') }}</textarea>
        </div> --}}

        <button type="submit" class="btn btn-primary">Guardar Maquinaria</button>
    </form>
</div>
@endsection {{-- ¡NO DEBE HABER UN </html> DESPUÉS DE ESTO! --}}
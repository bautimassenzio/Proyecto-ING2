@extends('layouts.base') {{-- ESTA LÍNEA ES CORRECTA --}}

@section('content') {{-- Y ESTO DEBE CONTENER SOLAMENTE EL CONTENIDO --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Editar Maquinaria: {{ $maquinaria->nro_inventario }}</h1>
        </div>

        <!-- Mensajes de éxito/error de sesión -->
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
            @csrf <!-- Token CSRF para protección contra ataques -->
            @method('PUT') <!-- Indicar a Laravel que esta es una solicitud PUT -->

            <div class="mb-3">
                
                <!-- Precio por Día -->
                <div>
                    <label for="precio_dia" class="form-label">Precio por Día</label>
                    <input type="number" name="precio_dia" id="precio_dia" step="0.01"
                           value="{{ old('precio_dia', $maquinaria->precio_dia) }}"
                           class="form-control">
                    @error('precio_dia')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado"
                            class="form-select">
                        <option value="disponible" {{ old('estado', $maquinaria->estado) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="inactiva" {{ old('estado', $maquinaria->estado) == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                    </select>
                    @error('estado')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

            <!-- Botones de Acción -->
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
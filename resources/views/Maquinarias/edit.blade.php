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
                <!-- Nro. Inventario -->
                <div>
                    <label for="nro_inventario" class="form-label">Número de Inventario</label>
                    <input type="text" name="nro_inventario" id="nro_inventario" value="{{ old('nro_inventario', $maquinaria->nro_inventario) }}" class="form-control">
                    @error('nro_inventario')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

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

                <!-- Marca -->
                <div>
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" name="marca" id="marca"
                           value="{{ old('marca', $maquinaria->marca) }}"
                           class="form-control">
                    @error('marca')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modelo -->
                <div>
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" name="modelo" id="modelo"
                           value="{{ old('modelo', $maquinaria->modelo) }}"
                           class="form-control">
                    @error('modelo')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Localidad -->
                <div>
                    <label for="localidad" class="form-label">Localidad</label>
                    <input type="text" name="localidad" id="localidad"
                           value="{{ old('localidad', $maquinaria->localidad) }}"
                           class="form-control">
                    @error('localidad')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Año -->
                <div>
                    <label for="anio" class="form-label">Año</label>
                    <input type="number" name="anio" id="anio"
                           value="{{ old('anio', $maquinaria->anio) }}"
                           class="form-control">
                    @error('anio')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Uso -->
                <div>
                    <label for="uso" class="form-label">Uso</label>
                    <input type="text" name="uso" id="uso"
                           value="{{ old('uso', $maquinaria->uso) }}"
                           class="form-control">
                    @error('uso')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Energía -->
                <div>
                    <label for="tipo_energia" class="form-label">Tipo de Energía</label>
                    <select name="tipo_energia" id="tipo_energia"
                            class="form-select">
                        <option value="electrica" {{ old('tipo_energia', $maquinaria->tipo_energia) == 'electrica' ? 'selected' : '' }}>Eléctrica</option>
                        <option value="combustion" {{ old('tipo_energia', $maquinaria->tipo_energia) == 'combustion' ? 'selected' : '' }}>Combustión</option>
                    </select>
                    @error('tipo_energia')
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

                <!-- Política -->
                <div>
                    <label for="id_politica" class="form-label">
                    Política Asociada:
                    </label>
                    <select name="id_politica" id="id_politica" required
                    class="form-select">
                        @foreach ($politicas as $politica)
                        <option value="{{ $politica->id_politica }}"
                        {{ old('id_politica', $maquinaria->id_politica ?? '') == $politica->id_politica ? 'selected' : '' }}>
                        {{ $politica->tipo }} @if($politica->detalle) ({{ $politica->detalle }}) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('id_politica')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto Actual (si existe) y Campo de Carga de Nueva Foto -->
                <div class="mb-3">
                    <label for="foto_url" class="form-label">Foto de la Maquinaria</label>
                    @if ($maquinaria->foto_url)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Foto actual:</p>
                            <img src="{{ Storage::url($maquinaria->foto_url) }}" alt="Foto de {{ $maquinaria->nro_inventario }}" class="w-48 h-auto object-cover rounded-md shadow-md">
                        </div>
                    @else
                        <p class="text-sm text-gray-600 mb-2">No hay foto actual.</p>
                    @endif
                    <input type="file" name="foto_url" id="foto_url"
                           class="form-control">
                    <p class="mt-1 text-xs text-gray-500">Solo JPG, JPEG, PNG. Tamaño máximo 5MB.</p>
                    @error('foto_url')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
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
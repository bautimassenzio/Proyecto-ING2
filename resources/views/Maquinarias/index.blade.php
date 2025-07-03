@extends($layout)

@section('content')
<div class="container">
    <h1 class="mb-4">Catálogo de Maquinarias</h1>

    <!-- Botón para mostrar/ocultar filtros -->
    <button class="btn btn-outline-secondary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="{{ request()->hasAny(['precio_min','precio_max','uso','localidad','tipo_energia','id_politica']) ? 'true' : 'false' }}" aria-controls="filtrosCollapse">
        Filtros <i class="bi bi-funnel-fill"></i>
    </button>

    <div class="collapse {{ request()->hasAny(['precio_min','precio_max','uso','localidad','tipo_energia','id_politica']) ? 'show' : '' }}" id="filtrosCollapse">
        <form method="GET" action="{{ route('catalogo.index') }}" class="mb-4 border p-3 rounded bg-light">
            <div class="row g-3 align-items-end">

                <div class="col-md-2">
                    <label for="precio_min" class="form-label">Precio Mínimo</label>
                    <input type="number" min="0" name="precio_min" id="precio_min" class="form-control" value="{{ request('precio_min') }}">
                </div>

                <div class="col-md-2">
                    <label for="precio_max" class="form-label">Precio Máximo</label>
                    <input type="number" min="0" name="precio_max" id="precio_max" class="form-control" value="{{ request('precio_max') }}">
                </div>

                <div class="col-md-2">
                    <label for="tipo_de_uso_id" class="form-label">Tipo de Uso</label>
                    <select name="tipo_de_uso_id" id="tipo_de_uso_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($tiposDeUso as $tipo)
                            <option value="{{ $tipo->id }}" {{ request('tipo_de_uso_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="localidad_id" class="form-label">Localidad</label>
                    <select name="localidad_id" id="localidad_id" class="form-control">
                        <option value="">Todas</option>
                        @foreach($localidades as $localidad)
                            <option value="{{ $localidad->id }}" {{ request('localidad_id') == $localidad->id ? 'selected' : '' }}>
                                {{ $localidad->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-2">
                    <label for="tipo_energia" class="form-label">Tipo de Energía</label>
                    <select name="tipo_energia" id="tipo_energia" class="form-select">
                        <option value="">-- Cualquiera --</option>
                        <option value="electrica" {{ request('tipo_energia') == 'electrica' ? 'selected' : '' }}>Eléctrica</option>
                        <option value="combustion" {{ request('tipo_energia') == 'combustion' ? 'selected' : '' }}>Combustión</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="id_politica" class="form-label">Política de Cancelación</label>
                    <select name="id_politica" id="id_politica" class="form-select">
                        <option value="">-- Cualquiera --</option>
                        @foreach(\App\Domain\Maquinaria\Models\Politica::all() as $politica)
                            <option value="{{ $politica->id_politica }}" {{ request('id_politica') == $politica->id_politica ? 'selected' : '' }}>
                                {{ $politica->tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('catalogo.index') }}" class="btn btn-outline-secondary text-dark" style="color:#333 !important;">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="row">
        @forelse($maquinarias as $maquinaria)
            <!-- Aquí va tu código para mostrar cada tarjeta (sin cambios) -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 maquinaria-card">
                    @if($maquinaria->foto_url)
                        <img src="{{ asset('storage/' . $maquinaria->foto_url) }}" class="card-img-top" alt="Imagen de la maquinaria">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $maquinaria->marca }} {{ $maquinaria->modelo }}</h5>
                        <p class="card-text">
                            <strong>Precio por día:</strong> ${{ $maquinaria->precio_dia }}<br>
                            <strong>Estado:</strong> {{ ucfirst($maquinaria->estado) }}<br>
                            <strong>Localidad:</strong> {{ $maquinaria->localidad->nombre ?? 'No especificada' }}<br>
                            <strong>Descripción:</strong> {{ $maquinaria->descripcion }}
                        </p>
                    </div>
                    <div class="card-footer text-center">
                        <!-- Lógica de botones según roles y estado -->
                        @auth('users')
                            @if(Auth::guard('users')->user()->rol === 'admin')
                                <a href="{{ route('maquinarias.edit', $maquinaria->id_maquinaria) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('maquinarias.destroy', $maquinaria->id_maquinaria) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro que deseas dar de baja esta maquinaria?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Dar de Baja</button>
                                </form>
                            @elseif(Auth::guard('users')->user()->rol === 'cliente' && $maquinaria->estado === 'disponible')
                                <a href="{{ url('reservas/crear?id_maquinaria=' . $maquinaria->id_maquinaria) }}" class="btn btn-primary btn-sm">Reservar</a>
                            @else
                                <span class="text-muted">Solo visualización</span>
                            @endif
                        @endauth

                        @guest('users')
                            @if($maquinaria->estado === 'disponible')
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Reservar</a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No hay maquinarias disponibles en este momento.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('additional-styles')
<style>
    /* Opcional: icono filtro - si usás Bootstrap Icons, sino sacalo */
    .bi-funnel-fill {
        vertical-align: middle;
        margin-left: 5px;
    }
</style>
@endsection
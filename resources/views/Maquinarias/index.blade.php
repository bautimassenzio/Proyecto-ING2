@extends($layout)


@section('content')
<div class="container">
    <h1 class="mb-4">Catálogo de Maquinarias</h1>

    

    <div class="row">
        
       @forelse($maquinarias as $maquinaria)
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
                    <strong>Localidad:</strong> {{ $maquinaria->localidad }}<br>
                    <strong>Descripcion:</strong> {{ $maquinaria->descripcion }}
                </p>
            </div>
            <div class="card-footer text-center">
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
    .maquinaria-card {
        border-radius: 1rem;
        box-shadow: var(--shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        background-color: white;
    }

    .maquinaria-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .maquinaria-card img {
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
    width: 100%;
    height: auto;
    max-height: 300px; /* Más alargada que antes */
    object-fit: contain;
    background-color: #f9f9f9; /* Opcional para relleno neutro */
}


    .maquinaria-card .card-body {
        padding: 1rem;
    }

    .maquinaria-card .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .maquinaria-card .card-footer {
        background: none;
        border-top: 1px solid #eee;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        padding: 1rem;
    }
</style>
@endsection



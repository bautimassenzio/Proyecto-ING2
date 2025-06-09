@extends($layout)

@section('content')
<div class="container">
    <h1 class="mb-4">Catálogo de Maquinarias</h1>

    <div class="row">
        @foreach($maquinarias as $maquinaria)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($maquinaria->foto_url)
                        <img src="{{ asset('storage/' . $maquinaria->foto_url) }}" class="card-img-top" alt="Imagen de la maquinaria">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $maquinaria->marca }} {{ $maquinaria->modelo }}</h5>
                        <p class="card-text">
                            <strong>Precio por día:</strong> ${{ $maquinaria->precio_dia }}<br>
                            <strong>Estado:</strong> {{ ucfirst($maquinaria->estado) }}<br>
                            <strong>Localidad:</strong> {{ $maquinaria->localidad }}
                        </p>
                    </div>
                    <div class="card-footer text-center">
                        {{-- Lógica de botones según el tipo de usuario --}}
                        {{-- CAMBIO CLAVE AQUÍ: Usamos @auth('users') para el guard 'users' --}}
                        @auth('users')
                            {{-- El usuario está autenticado en el guard 'users' --}}
                            {{-- CAMBIO CLAVE AQUÍ: Usamos Auth::guard('users')->user() --}}
                            @if(Auth::guard('users')->user()->rol === 'admin') {{-- Nota: Usé 'rol' en minúscula según tu modelo/DB --}}
                                <a href="{{ route('maquinarias.edit', $maquinaria->id_maquinaria) }}" class="btn btn-warning btn-sm">Editar</a>

                                <form action="{{ route('maquinarias.destroy', $maquinaria->id_maquinaria) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro que deseas dar de baja esta maquinaria?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Dar de Baja</button>
                                </form>
                            {{-- CAMBIO CLAVE AQUÍ: Usamos Auth::guard('users')->user() --}}
                            @elseif(Auth::guard('users')->user()->rol === 'cliente' && $maquinaria->estado === 'disponible')
                                {{-- Asegúrate de que esta URL sea correcta. Tu ruta `reservas.create` espera `id_maquinaria`. --}}
                                <a href="{{ url('reservas/crear?id_maquinaria=' . $maquinaria->id_maquinaria) }}" class="btn btn-primary btn-sm">Reservar</a>
                            @else
                                {{-- Usuarios autenticados que no son admin ni cliente, solo ven --}}
                                <span class="text-muted">Solo visualización</span>
                            @endif
                        @endauth

                        {{-- CAMBIO CLAVE AQUÍ: Usamos @guest('users') para el guard 'users' --}}
                        @guest('users')
                            {{-- El usuario NO está autenticado en el guard 'users' --}}
                            @if($maquinaria->estado === 'disponible')
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Reservar</a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
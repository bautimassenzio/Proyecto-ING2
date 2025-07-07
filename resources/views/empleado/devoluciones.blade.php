@extends('layouts.base')

@section('contenido')
    <h2>Reservas pendientes de devolución</h2>

    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    @forelse ($reservas as $reserva)
        <div class="card mb-3">
            <div class="card-body">
                <h5>{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</h5>
                <p>Cliente: {{ $reserva->cliente->nombre }}</p>
                <p>Desde: {{ $reserva->fecha_inicio }} | Hasta: {{ $reserva->fecha_fin }}</p>
<form method="POST" action="{{ route('empleado.devolver', $reserva->id_reserva) }}">
    @csrf
    <button type="submit" class="btn btn-primary">Registrar Devolución</button>
</form>

            </div>
        </div>
    @empty
        <p>No hay reservas pendientes de devolución.</p>
    @endforelse
@endsection

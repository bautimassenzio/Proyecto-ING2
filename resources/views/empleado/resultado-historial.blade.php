@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Historial de reservas de {{ $cliente->nombre }} {{ $cliente->apellido }}</h2>

    @if($reservas->isEmpty())
        <p>No se encontraron reservas para este cliente.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Maquinaria</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Estado</th>
                    <th>Precio total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservas as $reserva)
                    <tr>
                        <td>{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</td>
                        <td>{{ $reserva->fecha_inicio }}</td>
                        <td>{{ $reserva->fecha_fin }}</td>
                        <td>{{ ucfirst($reserva->estado) }}</td>
                        <td>${{ number_format($reserva->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('empleado.historial.formulario') }}" class="btn btn-secondary">Volver a selección</a>
</div>
@endsection

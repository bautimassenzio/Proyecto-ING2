@extends($layout)

@section('title', 'Mis Reservas')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Historial de Reservas</h2>

    {{-- Mensaje de error desde el controlador --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($reservas->isEmpty())
        <div class="alert alert-info">
            No tienes reservas registradas.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Maquinaria</th>
                        <th>Fecha Reserva</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Días</th>
                        <th>Total (ARS)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                        <th>Pagar</th> <!-- Nueva columna -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservas as $reserva)
                        <tr>
                            <td>{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</td>
                            <td>{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->diffInDays($reserva->fecha_fin) }}</td>
                            <td>${{ number_format($reserva->total, 2) }}</td>
                            <td>
                                @php
                                    $estado = $reserva->estado;
                                    $color = match($estado) {
                                        'pendiente' => 'warning',
                                        'activa' => 'success',
                                        'aprobada' => 'success',
                                        'completada' => 'secondary',
                                        'cancelada' => 'danger',
                                        default => 'light'
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ ucfirst($estado) }}</span>
                            </td>
                            <td>
                                <form action="{{ route('reservas.cancelar', $reserva->id_reserva) }}" method="POST" onsubmit="return confirm('¿Confirmas cancelar esta reserva?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('reservas.pagarDesdeHistorial', $reserva->id_reserva) }}" class="btn btn-success btn-sm">Pagar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <a href="{{ route('catalogo.index') }}" class="btn btn-primary mt-4">Volver al Catálogo</a>
</div>
@endsection

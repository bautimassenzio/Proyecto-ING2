@extends($layout) {{-- Utiliza la variable $layout para tu layout base --}}

@section('title', 'Reservas Pendientes de Devolución')

@section('content')
    <h1>Reservas Pendientes de Devolución</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($reservas->isEmpty())
        <p>No hay reservas pendientes de devolución en este momento.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Reserva</th>
                    <th>Cliente</th>
                    <th>Maquinaria</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Fecha Entrega</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservas as $reserva)
                    <tr>
                        <td>{{ $reserva->id_reserva }}</td>
                        <td>{{ $reserva->cliente->nombre }} ({{ $reserva->cliente->dni }})</td>
                        <td>{{ $reserva->maquinaria->nombre }}</td>
                        <td>{{ $reserva->fecha_inicio->format('d/m/Y') }}</td>
                        <td>{{ $reserva->fecha_fin->format('d/m/Y') }}</td>
                        <td>{{ $reserva->fecha_entrega ? $reserva->fecha_entrega->format('d/m/Y H:i') : 'N/A' }}</td>
                        <td>
                            <form action="{{ route('empleado.registrar-devolucion', $reserva->id_reserva) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Marcar como Devuelta</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
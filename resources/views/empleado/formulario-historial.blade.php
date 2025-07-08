@extends('layouts.base')

@section('content')
<div class="container">
    <h2>Consultar historial de reservas</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('empleado.historial.mostrar', ['dni' => 'DNI_A_REEMPLAZAR']) }}" method="GET" id="historialForm">
        <div class="form-group">
            <label for="cliente">Seleccionar cliente:</label>
            <select name="dni" id="clienteSelect" class="form-control">
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->dni }}">{{ $cliente->nombre }} {{ $cliente->apellido }} (DNI: {{ $cliente->dni }})</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Ver historial</button>
    </form>
</div>

<script>
    // Redirige dinámicamente al DNI seleccionado
    document.getElementById('historialForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let dni = document.getElementById('clienteSelect').value;
        let action = this.action.replace('DNI_A_REEMPLAZAR', dni);
        window.location.href = action;
    });
</script>
@endsection

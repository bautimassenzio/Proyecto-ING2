{{-- resources/views/pago/tarjeta.blade.php --}}

@extends('layouts.base')

@section('title', 'Pago con Tarjeta')

@section('navigation')
    @auth('users')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('catalogo.index') }}">Catálogo</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('reservas.index') }}">Mis Reservas</a>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">Login</a>
        </li>
        @if (Route::has('register'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
            </li>
        @endif
    @endauth
    <li class="nav-item">
        <a class="nav-link" href="{{ route('pago.seleccionar') }}">Seleccionar Pago</a>
    </li>
    {{-- La siguiente línea en tu navegación no es necesaria si este es el formulario actual.
         Normalmente, un "active" sería para la página actual, pero procesar.pago.tarjeta
         es la acción POST, no la vista GET. La ruta para esta vista es 'pago.formularioTarjeta'.
         Considera cambiarla a: <a class="nav-link active" aria-current="page" href="{{ route('pago.formularioTarjeta') }}">Pago con Tarjeta</a>
    --}}
    {{-- <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="{{ route('procesar.pago.tarjeta') }}">Pago con Tarjeta</a>
    </li> --}}
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title text-center">Pago con Tarjeta</h1>
            </div>
            <div class="card-body">
                

                <form action="{{ route('pago.procesar.tarjeta') }}" method="POST">
                    @csrf

                    {{-- ¡AÑADE ESTA LÍNEA! --}}
                    {{-- El valor de $reserva_id viene del controlador que renderiza esta vista --}}
                    <input type="hidden" name="reserva_id" value="{{ $reserva_id ?? '' }}">

                    <div class="mb-3">
                        <label for="card_number" class="form-label">Número de Tarjeta:</label>
                        <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX"
                               class="form-control"
                               pattern="^\d{4} \d{4} \d{4} \d{4}$"
                               title="Ingrese el número de tarjeta en el formato XXXX XXXX XXXX XXXX"
                               required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="expiry_month" class="form-label">Mes de Vencimiento:</label>
                            <select id="expiry_month" name="expiry_month" class="form-select" required>
                                <option value="">Seleccionar Mes</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_year" class="form-label">Año de Vencimiento:</label>
                            <select id="expiry_year" name="expiry_year" class="form-select" required>
                                <option value="">Seleccionar Año</option>
                                @php
                                    $currentYear = intval(date('y'));
                                    for ($i = 0; $i <= 10; $i++) {
                                        $year = $currentYear + $i;
                                        echo "<option value='".sprintf('%02d', $year)."'>20".sprintf('%02d', $year)."</option>";
                                    }
                                @endphp
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cvv" class="form-label">CVV:</label>
                        <input type="text" id="cvv" name="cvv" placeholder="XXX o XXXX" class="form-control" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Pagar</button>
                        <a href="{{ route('pago.seleccionar') }}" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- resources/views/pago/estado.blade.php --}}

@extends('layouts.base') {{-- CAMBIO AQUÍ: Ruta del layout codificada --}}

@section('title', 'Estado de Pago')

{{-- Opcional: Define la navegación para esta vista si la necesitas --}}

@section('content')
<div class="row justify-content-center text-center">
    <div class="col-md-8 col-lg-6">
        @if ($mensaje)
            <h1 class="mb-4">{{ $mensaje }}</h1>
        @else
            <h1 class="mb-4">Estado de Pago Desconocido</h1>
        @endif

        {{-- Formulario para volver al inicio --}}
        <form action="{{ url('/') }}" method="get"> {{-- MEJORA AQUÍ: Usar url('/') en lugar de http://127.0.0.1:8000 --}}
            <button type="submit" class="btn btn-primary btn-lg">Volver al inicio</button>
        </form>
    </div>
</div>
@endsection
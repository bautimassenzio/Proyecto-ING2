{{-- resources/views/contacto/index.blade.php --}}

@extends('layouts.base') {{-- Asegúrate de que 'layouts.base' sea el nombre correcto de tu layout --}}

@section('title', 'Información de Contacto') {{-- Título específico para esta página --}}

{{-- Opcional: Define la navegación si la necesitas para esta vista --}}
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
        <a class="nav-link active" aria-current="page" href=>Contacto</a>
    </li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <h1 class="text-center mb-4">Información de Contacto</h1>

        <div class="contact-person mb-4 pb-3 border-bottom"> {{-- Clases de Bootstrap para margen y borde --}}
            <h2 class="person-name text-primary mb-2">[Nombre del Personal 1]</h2> {{-- Clases de Bootstrap y tu color primary --}}
            <div class="contact-info ms-3"> {{-- Margen izquierdo con Bootstrap --}}
                <p class="mb-1"><strong>Dirección:</strong> [Dirección del Personal 1], La Plata, Buenos Aires</p>
                <p class="mb-1"><strong>Teléfono:</strong> [Número de Teléfono del Personal 1]</p>
                <p class="mb-0"><strong>Correo Electrónico:</strong> <a href="mailto:[Correo del Personal 1]" class="text-info">[Correo del Personal 1]</a></p> {{-- Clase de Bootstrap para el color del enlace --}}
            </div>
        </div>

        <div class="contact-person mb-0 pb-0"> {{-- Última persona sin margen inferior ni borde --}}
            <h2 class="person-name text-primary mb-2">[Nombre del Personal 2]</h2>
            <div class="contact-info ms-3">
                <p class="mb-1"><strong>Dirección:</strong> [Dirección del Personal 2], La Plata, Buenos Aires</p>
                <p class="mb-1"><strong>Teléfono:</strong> [Número de Teléfono del Personal 2]</p>
                <p class="mb-0"><strong>Correo Electrónico:</strong> <a href="mailto:[Correo del Personal 2]" class="text-info">[Correo del Personal 2]</a></p>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="javascript:history.back()" class="back-link text-muted">Volver</a> {{-- Usamos tu clase back-link y clases de Bootstrap --}}
        </div>
    </div>
</div>
@endsection

{{-- No se requieren scripts o estilos adicionales específicos para esta vista --}}
{{-- Los estilos que tenías incrustados para .contact-container, h1, .contact-person, etc.
     puedes considerarlos para añadirlos a tu CSS principal si son reutilizables,
     o dejarlos aquí si solo son para esta página específica (pero no se recomienda).
     Para este ejemplo, he añadido clases de Bootstrap que ya tienes en tu layout. --}}
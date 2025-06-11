@extends('layouts.base')

@section('title', 'MaquinaTech - Inicio')

{{-- Los enlaces de navegación 'Inicio' y 'Catálogo' se inyectan en la sección 'navigation' del base.
     Para centrarlos, necesitaremos CSS adicional en 'additional-styles'. --}}
@section('navigation')
    <li class="nav-item">
        <a href="/" class="nav-link {{ request()->routeIs('/') ? 'active' : '' }}">
            <i class="fas fa-home me-1"></i> Inicio
        </a>
    </li>
    <li class="nav-item">
        <a href="/catalogo" class="nav-link {{ request()->routeIs('catalogo') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart me-1"></i> Catálogo
    </li>
@endsection

{{-- Aquí inyectamos los botones de iniciar sesión y registrarse
     cuando el usuario NO está autenticado, usando la sección 'guest-navigation' del base. --}}
@section('guest-navigation')
    <div class="d-flex gap-3 align-items-center">
            <div>
                <a href="{{ route('login') }}"  class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión
                </a>
            </div>
         <div>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Registrarse
                </a>
            </div>
    </div>
@endsection

{{-- Secciones de estilo adicionales para el visitante --}}
@section('additional-styles')
<style>
    

    .btn-primary {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--secondary-yellow) 100%);
            border: none;
            color: var(--dark-bg);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            width: max-content;
        }

    /* Personalizaciones para la navbar del visitante, sin tocar el base */

    /* Centrar "Inicio" y "Catálogo" */
    /*
     * El 'me-auto' en .navbar-nav en el base lo empuja a la izquierda por defecto.
     * Para centrarlo, podemos intentar anular el 'me-auto' y usar 'mx-auto' o 'justify-content-center'.
     * Pero esto es complicado sin modificar el HTML del base.
     * Una forma es que el `ul.navbar-nav` ocupe todo el ancho y sus hijos se centren.
     */
   

    @media (min-width: 992px) { /* Para pantallas grandes, donde se ve la navbar extendida */
        /* Sobrescribir el margin-right: auto de Bootstrap */
        .navbar-collapse .navbar-nav.me-auto {
            margin-right: 0 !important; /* Quita el me-auto que empuja a la izquierda */
            width: 100%; /* Ocupa el 100% del espacio disponible */
            justify-content: center; /* Centra los items de navegación */
        }
    }

    /* Ajustar el footer si es diferente del base y no podemos modificar el base.
       Sin embargo, si el base ya tiene un footer, este CSS podría no aplicarse
       o aplicarse incorrectamente si las clases no coinciden.
       Lo ideal es que el footer de "MaquinaTech" esté en el base si es el deseado.
       Dado que no podemos modificar el base, el footer de "MannyMaquinarias" es el que se mostrará.
    */
    

    .footer-links .footer-link:hover {
        color: var(--secondary-yellow);
    }

    .copyright {
        color: #aaa;
        font-size: 0.9rem;
    }
    
    /* FIN DE BLOQUE DE FOOTER A ELIMINAR */

    /* Responsive footer (esto también debe ser del base si el base tiene un footer) */
    /*
    @media (max-width: 768px) {
        .footer-container {
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
        }

        .footer-links {
            flex-direction: column;
            gap: 1rem;
        }
    }
    */
</style>
@endsection

@section('content')
    {{-- Aquí va el contenido específico de la página del visitante --}}
    <div class="text-center my-5">
        <h1>¡Bienvenido a MaquinaTech!</h1>
        <p class="lead">Descubre nuestra amplia gama de maquinarias.</p>
    </div>
@endsection
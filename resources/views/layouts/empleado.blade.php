@extends('layouts.base')

@section('navigation')
<li class="nav-item">
    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
        <i class="fas fa-home me-1"></i> Inicio
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('registerByEmployee') ? 'active' : '' }}" href="{{ url('/registerByEmployee') }}">
        <i class="fas fa-user-plus me-1"></i> Registrar Cliente
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->is('/empleado/historial-clientes') ? 'active' : '' }}" href="{{ url('/empleado/historial-clientes') }}">
        <i class="fas fa-history me-1"></i> Consultar historial cliente
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->is('empleado/panel-entregas-devoluciones*') || request()->is('empleado/entregas-pendientes') || request()->is('empleado/devoluciones-pendientes') ? 'active' : '' }}" 
        href="{{ route('empleado.panel-entregas-devoluciones') }}">
        <i class="fas fa-truck-ramp-box me-1"></i> Entregas y Devoluciones
    </a>
</li>

{{-- ** ELIMINADO del Navbar: Gestionar Usuarios, Pedidos Pendientes, Maquinarias, Clientes ** --}}

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-user-circle me-1"></i> Mi Cuenta
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('passwordReset') }}">
            <i class="fas fa-key me-2"></i> Cambiar Contraseña
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="dropdown-item text-danger" type="submit">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </button>
            </form>
        </li>
    </ul>
</li>
@endsection
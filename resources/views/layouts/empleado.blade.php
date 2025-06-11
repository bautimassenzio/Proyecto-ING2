@extends('layouts.base')

@section('navigation')
<li class="nav-item">
    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
        <i class="fas fa-home me-1"></i> Dashboard
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ url('/users') }}">
        <i class="fas fa-users me-1"></i> Gestionar Usuarios
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('registerByEmployee') ? 'active' : '' }}" href="{{ url('/registerByEmployee') }}">
        <i class="fas fa-user-plus me-1"></i> Registrar Cliente
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pedidos-pendientes') ? 'active' : '' }}" href="{{ url('pedidos-pendientes') }}">
        <i class="fas fa-clipboard-check me-1"></i> Pedidos Pendientes
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('maquinarias') ? 'active' : '' }}" href="{{ url('maquinarias') }}">
        <i class="fas fa-cogs me-1"></i> Maquinarias
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('clientes') ? 'active' : '' }}" href="{{ url('clientes') }}">
        <i class="fas fa-users-cog me-1"></i> Clientes
    </a>
</li>


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

@section('additional-styles')
<style>
    .employee-dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .task-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-top: 4px solid var(--primary-yellow);
        transition: transform 0.3s ease;
    }

    .task-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .task-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .task-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-bg);
    }

    .task-title {
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .task-count {
        background: var(--light-yellow);
        color: var(--dark-yellow);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: auto;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }

    .quick-action {
        background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
        color: var(--dark-bg);
        padding: 1rem;
        border-radius: 10px;
        text-decoration: none;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .quick-action:hover {
        background: linear-gradient(135deg, var(--secondary-yellow), var(--dark-yellow));
        transform: translateY(-2px);
        color: var(--dark-bg);
    }

    .quick-action i {
        display: block;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
</style>
@endsection
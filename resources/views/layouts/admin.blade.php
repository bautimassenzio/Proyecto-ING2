@extends('layouts.base')

@section('navigation')
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('/') ? 'active' : '' }}" href="{{ route('/') }}">
        <i class="fas fa-home me-1"></i> Inicio
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->is('admin/maquinarias/create') ? 'active' : '' }}" href="{{ url('admin/maquinarias/create') }}">
        <i class="fas fa-cogs me-1"></i> Agregar maquinarias
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('catalogo') ? 'active' : '' }}" href="{{ url('catalogo') }}">
        <i class="fas fa-cogs me-1"></i> Maquinarias
    </a>
</li>

   <!-- <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-clipboard-list me-1"></i> Pedidos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-chart-bar me-1"></i> Reportes
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-cog me-1"></i> Configuración
        </a>
    </li>
-->
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
    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-left: 4px solid var(--primary-yellow);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-bg);
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: var(--text-light);
        font-weight: 500;
    }

    .admin-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }

    .action-btn {
        background: white;
        border: 2px solid var(--primary-yellow);
        border-radius: 12px;
        padding: 1.5rem;
        text-decoration: none;
        color: var(--text-dark);
        transition: all 0.3s ease;
        text-align: center;
    }

    .action-btn:hover {
        background: var(--primary-yellow);
        color: var(--dark-bg);
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(255, 184, 0, 0.3);
    }

    .action-btn i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }
</style>
@endsection
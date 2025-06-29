@extends('layouts.base')

@section('navigation')

@stack('scripts')

<li class="nav-item">
    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
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

{{-- ** MENÚ DESPLEGABLE PARA ESTADÍSTICAS ** --}}
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->is('admin/estadisticas*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-chart-line me-1"></i> Estadísticas
    </a>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('admin.estadisticas.nuevos-clientes') }}">
                <i class="fas fa-user-plus me-2"></i> Nuevos Clientes Registrados
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('admin.estadisticas.maquinas-mas-alquiladas') }}">
                <i class="fas fa-truck-moving me-2"></i> Maquinarias Más Alquiladas
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('admin.estadisticas.ingresos') }}">
                <i class="fas fa-money-bill-wave me-2"></i> Ingresos
            </a>
        </li>
    </ul>
</li>
{{-- FIN DEL MENÚ DESPLEGABLE --}}

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
    /* Definición de variables CSS - asegúrate de que estén definidas en un lugar accesible,
       si no están ya en layouts.base o un CSS global. Si están aquí, solo son para este layout. */
    :root {
        --primary-yellow: #FFC107; /* Un amarillo ejemplo, ajusta al que uses */
        --secondary-yellow: #FFD54F; /* Un amarillo más claro, ajusta al que uses */
        --dark-bg: #343a40; /* Color de fondo oscuro, ajusta al que uses */
        --text-dark: #212529; /* Color de texto oscuro, ajusta al que uses */
        --text-light: #6c757d; /* Color de texto claro, ajusta al que uses */
    }

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
        padding: 1.5rem; /* Este padding es grande para botones de acción */
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

    .btn-primary-small {
        background-color: var(--primary-yellow);
        color: var(--dark-bg);
        font-weight: bold;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        border: 2px solid var(--primary-yellow);
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-block;
        text-align: center;
        text-decoration: none;
        line-height: normal;
    }

    .btn-primary-small:hover {
        background-color: var(--secondary-yellow);
        border-color: var(--secondary-yellow);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 184, 0, 0.2);
        color: var(--dark-bg);
    }
</style>
@endsection

@extends('layouts.base')

@section('navigation')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('/') ? 'active' : '' }}" href="{{ route('/') }}">
            <i class="fas fa-home me-1"></i> Mi Panel
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="catalogo">
            <i class="fas fa-shopping-cart me-1"></i> Catálogo
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-clipboard-list me-1"></i> Mis Pedidos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-file-invoice me-1"></i> Cotizaciones
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-history me-1"></i> Historial
        </a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle me-1"></i> Mi Cuenta
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">
                <i class="fas fa-user-edit me-2"></i> Editar Perfil
            </a></li>
            <li><a class="dropdown-item" href="{{ route('passwordReset') }}">
                <i class="fas fa-key me-2"></i> Cambiar Contraseña
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-warning" href="{{ route('eliminarCuenta') }}">
                <i class="fas fa-user-times me-2"></i> Eliminar Cuenta
            </a></li>
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
    .client-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .overview-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-left: 4px solid var(--primary-yellow);
        transition: transform 0.3s ease;
    }

    .overview-card:hover {
        transform: translateY(-3px);
    }

    .overview-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-bg);
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }

    .overview-title {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .overview-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-yellow);
    }

    .client-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }

    .client-action {
        background: white;
        border: 2px solid var(--primary-yellow);
        border-radius: 12px;
        padding: 1.5rem;
        text-decoration: none;
        color: var(--text-dark);
        transition: all 0.3s ease;
        text-align: center;
    }

    .client-action:hover {
        background: var(--primary-yellow);
        color: var(--dark-bg);
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(255, 184, 0, 0.3);
    }

    .client-action i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .recent-activity {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 2rem;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 35px;
        height: 35px;
        background: var(--light-yellow);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark-yellow);
    }
</style>
@endsection
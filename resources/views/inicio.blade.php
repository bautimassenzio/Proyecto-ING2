@extends($layout)

@section('title', 'Dashboard - MannyMaquinarias')

@section('content')
<div class="dashboard-welcome">
    <div class="welcome-header">
        <h1 class="display-5 fw-bold text-primary mb-3">
            <i class="fas fa-hand-wave text-warning me-2"></i>
            ¡Bienvenido, {{ Auth::guard('users')->user()->nombre }}!
        </h1>
        <p class="lead text-muted">
            @if(Auth::guard('users')->user()->rol === 'admin')
                Gestiona el sistema completo desde tu panel de administración.
            @elseif(Auth::guard('users')->user()->rol === 'empleado')
                Administra usuarios y procesa pedidos desde tu panel de trabajo.
            @else
                Explora nuestro catálogo y gestiona tus pedidos fácilmente.
            @endif
        </p>
    </div>

    @if(Auth::guard('users')->user()->rol === 'admin')
        <!-- Admin Dashboard -->
         <!--
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number">156</div>
                <div class="stat-label">Usuarios Totales</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="stat-number">89</div>
                <div class="stat-label">Maquinarias</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-number">23</div>
                <div class="stat-label">Pedidos Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number">$45,230</div>
                <div class="stat-label">Ventas del Mes</div>
            </div>
        </div>
        -->

        <div class="admin-actions">
            
            <a href="/admin/maquinarias/create" class="action-btn">
                <i class="fas fa-plus-circle"></i>
                <div>Nueva Maquinaria</div>
            </a>
           <!-- <a href="#" class="action-btn">
                <i class="fas fa-chart-bar"></i>
                <div>Ver Reportes</div>
            </a>
            <a href="#" class="action-btn">
                <i class="fas fa-cog"></i>
                <div>Configuración</div>
            </a>
          -->
        </div>

    @elseif(Auth::guard('users')->user()->rol === 'empleado')
        <!-- Employee Dashboard -->
        <div class="employee-dashboard">
            <div class="task-card">
                <div class="task-header">
                    <div class="task-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h5 class="task-title">Pedidos Pendientes</h5>
                    <span class="task-count">8</span>
                </div>
                <p class="text-muted">Pedidos esperando confirmación</p>
                <a href="#" class="btn btn-outline-primary btn-sm">Ver Todos</a>
            </div>

            <div class="task-card">
                <div class="task-header">
                    <div class="task-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="task-title">Nuevos Clientes</h5>
                    <span class="task-count">3</span>
                </div>
                <p class="text-muted">Registros de esta semana</p>
                <a href="/users" class="btn btn-outline-primary btn-sm">Gestionar</a>
            </div>

            <div class="task-card">
                <div class="task-header">
                    <div class="task-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h5 class="task-title">Mantenimientos</h5>
                    <span class="task-count">2</span>
                </div>
                <p class="text-muted">Maquinarias en servicio</p>
                <a href="#" class="btn btn-outline-primary btn-sm">Ver Estado</a>
            </div>
        </div>

        <div class="quick-actions">
            <a href="/registerByEmployee" class="quick-action">
                <i class="fas fa-user-plus"></i>
                Registrar Cliente
            </a>
            <a href="#" class="quick-action">
                <i class="fas fa-clipboard-check"></i>
                Confirmar Pedidos
            </a>
            <a href="#" class="quick-action">
                <i class="fas fa-file-invoice"></i>
                Generar Cotización
            </a>
        </div>

    @else
        <!-- Client Dashboard -->
         <!--
     <div class="client-overview">  
            <div class="overview-card">
                <div class="overview-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="overview-title">Pedidos Activos</div>
                <div class="overview-value">3</div>
            </div>
            <div class="overview-card">
                <div class="overview-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="overview-title">Cotizaciones</div>
                <div class="overview-value">2</div>
            </div>
            <div class="overview-card">
                <div class="overview-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="overview-title">Pedidos Completados</div>
                <div class="overview-value">12</div>
            </div>
        </div>
````````-->
        <div class="client-actions">
            <a href="/catalogo" class="client-action">
                <i class="fas fa-search"></i>
                <div>Explorar Catálogo</div>
            </a>
            <!--
            <a href="#" class="client-action">
                <i class="fas fa-plus-circle"></i>
                <div>Nuevo Pedido</div>
            </a>

            <a href="#" class="client-action">
                <i class="fas fa-calculator"></i>
                <div>Solicitar Cotización</div>
            </a>
        
            <a href="#" class="client-action">
                <i class="fas fa-headset"></i>
                <div>Soporte Técnico</div>
            </a>
        </div>
        -->
        <!--
        <div class="recent-activity">
            <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Actividad Reciente</h5>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <strong>Pedido #1234</strong> completado exitosamente
                    <small class="text-muted d-block">Hace 2 días</small>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <strong>Cotización #5678</strong> generada
                    <small class="text-muted d-block">Hace 5 días</small>
                </div>
            </div>
        </div>
    @endif
</div>
-->

<style>
    .dashboard-welcome {
        animation: fadeInUp 0.6s ease-out;
    }

    .welcome-header {
        text-align: center;
        margin-bottom: 3rem;
        padding: 2rem;
        background: linear-gradient(135deg, var(--light-yellow) 0%, rgba(255, 184, 0, 0.1) 100%);
        border-radius: 16px;
        border: 1px solid rgba(255, 184, 0, 0.2);
    }

    .text-primary {
        color: var(--primary-yellow) !important;
    }

    @media (max-width: 768px) {
        .welcome-header {
            padding: 1.5rem;
        }
        
        .welcome-header h1 {
            font-size: 1.8rem;
        }
    }
</style>
@endsection
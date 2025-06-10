<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Sistema de Maquinarias')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-yellow: #FFB800;
            --secondary-yellow: #FFC82E;
            --dark-yellow: #E6A600;
            --light-yellow: #FFF3CC;
            --dark-bg: #1a1a1a;
            --light-bg: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--secondary-yellow) 100%);
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 3px solid var(--dark-yellow);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--dark-bg) !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

         .navbar-brand .logo-img {
            height: 70px; /* Ajusta esto según el tamaño de tu logo */
            width: auto; /* Mantiene la proporción */
            margin-right: 8px; /* Espacio entre el logo y el texto (si mantienes texto) */
        }

        .navbar-brand i {
            font-size: 1.8rem;
            color: var(--dark-bg);
        }

        .navbar-nav .nav-link {
            color: var(--dark-bg) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 0.2rem;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active {
            background-color: var(--dark-yellow);
            color: white !important;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: var(--dark-yellow);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-bg);
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--dark-bg);
            opacity: 0.8;
        }

        /* Main Content */
        .main-content {
            min-height: calc(100vh - 140px);
            padding: 2rem 0;
        }

        .content-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 184, 0, 0.1);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--secondary-yellow) 100%);
            border: none;
            color: var(--dark-bg);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-yellow) 0%, var(--dark-yellow) 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: var(--dark-bg);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-yellow);
            color: var(--primary-yellow);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-yellow);
            color: var(--dark-bg);
            transform: translateY(-2px);
        }

        /* Footer */
        .main-footer {
            background: var(--dark-bg);
            color: white;
            padding: 2rem 0;
            margin-top: auto;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-links a {
            color: var(--primary-yellow);
            text-decoration: none;
            margin: 0 1rem;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--secondary-yellow);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .navbar-nav {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                padding: 1rem;
                margin-top: 1rem;
            }

            .user-info {
                flex-direction: column;
                text-align: center;
                padding: 1rem;
            }

            .content-card {
                margin: 1rem;
                padding: 1.5rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-card {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Alert Styles */
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: var(--shadow);
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
    </style>

    @yield('additional-styles')
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Brand -->
                <a class="navbar-brand" href="{{ route('/') }}">
                    <img src="{{ asset('images/Manny_Maquinarias_Logov2.png') }}" alt="MannyMaquinarias Logo" class="logo-img">

                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        @yield('navigation')
                    </ul>

                    <!-- User Info -->
                    @auth('users')
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::guard('users')->user()->nombre, 0, 1)) }}
                            </div>
                            <div>
                                <div class="user-name">{{ Auth::guard('users')->user()->nombre }}</div>
                                <div class="user-role">
                                    @if(Auth::guard('users')->user()->rol === 'admin')
                                        <i class="fas fa-crown"></i> Administrador
                                    @elseif(Auth::guard('users')->user()->rol === 'empleado')
                                        <i class="fas fa-user-tie"></i> Empleado
                                    @else 
                                        <i class="fas fa-user"></i> Cliente
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        @yield('guest-navigation')
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Se encontraron errores:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            <div class="content-card">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <p class="-mb0">© {{ date('Y') }} MannyMaquinarias - Sistema de Gestión de Maquinarias</p>
                </div>
                <div class="footer-links">
                    <a href="/info-contactos"><i class="fas fa-phone"></i> Contacto</a>
                    <a href="/preguntas-frecuentes"><i class="fas fa-info-circle"></i> Preguntas Frecuentes</a>
                    
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('additional-scripts')
</body>
</html>
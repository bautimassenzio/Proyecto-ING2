<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Mi App')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; }
        header, footer { background: #007bff; color: white; padding: 1rem; text-align: center; }
        main { max-width: 800px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        nav a { color: white; margin: 0 10px; text-decoration: none; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        .btn { padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; font-weight: 600; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-primary { background: #007bff; color: white; }
        .btn + .btn { margin-left: 10px; }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido a la app</h1>
        <nav>
            @yield('navbar')
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>© {{ date('Y') }} - Mi App</p>
    </footer>
</body>
</html>
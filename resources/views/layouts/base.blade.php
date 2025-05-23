<!-- resources/views/layouts/base.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Mi App')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <h1>Bienvenido a la app</h1>
        @yield('navbar')
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>© {{ date('Y') }} - Mi App</p>
    </footer>
</body>
</html>

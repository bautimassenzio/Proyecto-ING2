<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Maquinarias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Lista de Maquinarias</h1>

        {{-- Aquí se mostrará el mensaje de éxito --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <p>Aquí irá el listado de tus maquinarias.</p>

        <a href="{{ route('maquinarias.create') }}" class="btn btn-primary">Crear Nueva Maquinaria</a>

        {{-- Aquí es donde iría el bucle para mostrar las maquinarias desde la base de datos --}}
        {{-- Por ahora, es solo un marcador de posición --}}

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
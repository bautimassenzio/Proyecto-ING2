<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Maquinarias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Listado de Maquinarias</h1>

        {{-- Bloque para mostrar el mensaje de éxito --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <a href="{{ route('maquinarias.create') }}" class="btn btn-primary mb-3">
            <i class="fas fa-plus-circle me-2"></i> Crear Nueva Maquinaria
        </a>

        @if ($maquinarias->isEmpty())
            <div class="alert alert-info" role="alert">
                No hay maquinarias registradas todavía.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nro. Inventario</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Precio/Día</th>
                            <th>Año</th>
                            <th>Uso</th>
                            <th>Energía</th>
                            <th>Estado</th>
                            <th>Localidad</th>
                            <th>Política</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($maquinarias as $maquinaria)
                            <tr>
                                <td>{{ $maquinaria->id_maquinaria }}</td> {{-- Usamos id_maquinaria como PK --}}
                                <td>{{ $maquinaria->nro_inventario }}</td>
                                <td>{{ $maquinaria->marca }}</td>
                                <td>{{ $maquinaria->modelo }}</td>
                                <td>${{ number_format($maquinaria->precio_dia, 2, ',', '.') }}</td>
                                <td>{{ $maquinaria->anio }}</td>
                                <td>{{ $maquinaria->uso }}</td>
                                <td>{{ $maquinaria->tipo_energia }}</td>
                                <td>{{ $maquinaria->estado }}</td>
                                <td>{{ $maquinaria->localidad }}</td>
                                <td>
                                    @if ($maquinaria->politica)
                                        {{ $maquinaria->politica->tipo }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if ($maquinaria->foto_url)
                                        <img src="{{ asset('storage/' . str_replace('public/', '', $maquinaria->foto_url)) }}" alt="Imagen de {{ $maquinaria->marca }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        Sin imagen
                                    @endif
                                </td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm me-1" title="Ver Detalles"><i class="fas fa-eye"></i> Ver</a>
                                    <a href="#" class="btn btn-warning btn-sm me-1" title="Editar"><i class="fas fa-edit"></i> Editar</a>
                                    <form action="#" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar esta maquinaria?');"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>
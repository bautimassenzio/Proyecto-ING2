@extends('layouts.admin')

@section('content')

<table class="table table-hover table-borderless text-center align-middle mb-0">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($usuarios as $usuario)
        <tr class="usuario-row">
            <td>{{ $usuario->nombre }}</td>
            <td>{{ $usuario->email }}</td>
            <td>
                {{-- Botón Editar (abre modal) --}}
                <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $usuario->dni }}">
                    Editar
                </button>

                @if ($usuario->estado === 'activo')
                    {{-- Botón Dar de baja --}}
                    <form action="{{ url('/users/' . $usuario->dni) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que querés dar de baja al usuario?')">
                            Dar de baja
                        </button>
                    </form>
                @else
                    {{-- Botón Dar de alta --}}
                    <form action="{{ url('/users/' . $usuario->dni . '/activate') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Querés dar de alta al usuario?')">
                            Dar de alta
                        </button>
                    </form>
                @endif
            </td>
        </tr>

        {{-- Modal de edición --}}
        <div class="modal fade" id="modalEditar{{ $usuario->dni }}" tabindex="-1" aria-labelledby="editarUsuarioLabel{{ $usuario->dni }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ url('/users/' . $usuario->dni) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title" id="editarUsuarioLabel{{ $usuario->dni }}">Editar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3 text-start">
                                <label for="nombre{{ $usuario->dni }}" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre{{ $usuario->dni }}" name="nombre" value="{{ $usuario->nombre }}" required>
                            </div>

                            <div class="mb-3 text-start">
                                <label for="email{{ $usuario->dni }}" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email{{ $usuario->dni }}" name="email" value="{{ $usuario->email }}" required>
                            </div>

                            <div class="mb-3 text-start">
                                <label for="telefono{{ $usuario->dni }}" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono{{ $usuario->dni }}" name="telefono" value="{{ $usuario->telefono }}">
                            </div>

                            <div class="mb-3 text-start">
                                <label for="rol{{ $usuario->dni }}" class="form-label">Rol</label>
                                <select class="form-select" id="rol{{ $usuario->dni }}" name="rol">
                                    <option value="cliente" {{ $usuario->rol === 'cliente' ? 'selected' : '' }}>Cliente</option>
                                    <option value="empleado" {{ $usuario->rol === 'empleado' ? 'selected' : '' }}>Empleado</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endforeach
    </tbody>
</table>

{{-- Paginación simple --}}
<div class="d-flex justify-content-center mt-3">
    {{ $usuarios->links() }}
</div>
@endsection

<style>
    .usuario-row:not(:last-child) {
        border-bottom: 1px solid #dee2e6;
    }

    .usuario-row:hover {
        background-color: #f8f9fa;
    }

    .btn-success {
        border: none;
        color: white;
        font-weight: 600 !important;
        padding: 0.75rem 1.5rem !important;
        border-radius: 10px !important;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }
</style>

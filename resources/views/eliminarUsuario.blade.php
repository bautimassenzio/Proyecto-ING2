@extends('layouts.admin')

@section('content')

@php
    $rolString = is_object($rol) ? $rol->value : $rol;
@endphp

<h2 class="text-center mb-4">
    @if (trim(strtolower($rolString)) === 'empleado')
        Listado de Empleados
    @else
        Listado de Clientes
    @endif
</h2>

@if ($usuarios->count() > 0)
    {{-- Formulario de búsqueda --}}
    <form method="GET" action="{{ route('usuarios.buscar', ['rol' => $rol]) }}" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre" value="{{ request('nombre') }}">
        </div>
        <div class="col-md-5">
            <input type="email" name="email" class="form-control" placeholder="Buscar por email" value="{{ request('email') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
    </form>
@endif


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
                            <label class="form-label">DNI</label>
                            <select class="form-select" disabled>
                                <option selected>{{ $usuario->dni }}</option>
                            </select>
                            <input type="hidden" name="dni" value="{{ $usuario->dni }}">
                            </div>
                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                                <input 
                                    type="date" 
                                    class="form-control"
                                    id="fecha_nacimiento" 
                                    name="fecha_nacimiento" 
                                    value="{{ $usuario->fecha_nacimiento }}"
                                    max="{{ date('Y-m-d') }}" 
                                    required
                                >
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
        @forelse ($usuarios as $usuario)
    
    @empty
    <tr>
    <td colspan="3">
        <div class="alert alert-info text-center fs-5 mb-0">
            No hay {{ strtolower($rolString) === 'empleado' ? 'empleados' : 'clientes' }} disponibles para mostrar.
        </div>
    </td>
</tr>

    @endforelse

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

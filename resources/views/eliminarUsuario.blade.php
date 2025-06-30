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
                {{-- Botón Editar --}}
                <a href="{{ url('/users/' . $usuario->dni . '/edit') }}" class="btn btn-primary btn-sm me-2">
                    Editar
                </a>

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
                        {{-- Usamos POST para activar --}}
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Querés dar de alta al usuario?')">
                            Dar de alta
                        </button>
                    </form>
                @endif
            </td>
        </tr>
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
</style>


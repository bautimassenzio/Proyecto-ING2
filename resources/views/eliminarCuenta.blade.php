@extends('layouts.base')

@section('title', 'Eliminar Cuenta')

@section('content')

{{-- Mensajes de éxito --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
                    <strong>Éxito:</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            {{-- Mensajes de error --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded" role="alert">
                    <strong>Se encontraron errores:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif
<div class="container mt-5 text-center">
    <h2 class="text-danger">Eliminar mi cuenta</h2>
    <p>¿Estás seguro de que querés eliminar tu cuenta? Esta acción <strong>no se puede deshacer</strong>.</p>

    <form id="delete-account-form" action="{{ route('eliminarCuenta') }}" method="POST">
        @csrf
        @method('DELETE')

        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Eliminar cuenta definitivamente</button>
        <a href="{{ route('/') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<script>
    function confirmDelete() {
        if (confirm('¿Estás seguro de que querés eliminar tu cuenta? Esta acción es irreversible.')) {
            document.getElementById('delete-account-form').submit();
        }
    }
</script>
@endsection

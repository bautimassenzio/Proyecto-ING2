@extends('layouts.base')

@section('title', 'Eliminar Cuenta')

@section('content')
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

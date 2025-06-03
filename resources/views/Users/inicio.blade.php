@extends($layout)

@section('title', 'Inicio')

@section('content')
<div class="text-center">
    <h2>Bienvenido, {{ Auth::guard('users')->user()->nombre }}</h2>
    <p>Este es tu panel según tu rol.</p>

    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
        @csrf
        <button class="btn btn-danger" type="submit">Cerrar Sesión</button>
    </form>

    <a href="{{ route('passwordReset') }}" class="btn btn-primary">Cambiar Contraseña</a>
</div>
@endsection

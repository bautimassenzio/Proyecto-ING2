{{-- resources/views/inicio.blade.php --}}
@extends($layout)

@section('title', 'Inicio')

@section('content')
    <h2>Bienvenido, {{ Auth::guard('users')->user()->nombre }}</h2>
    <p>Este es tu panel según tu rol.</p>
    <li class="nav-item">
        <form action="{{ route('logout' )}}" method="POST">
        @csrf
        <button class="nav-link" type="submit"> Cerrar Sesion</button>
        </form>
        
        <a href="{{ route('passwordReset') }}" class="btn btn-primary">
    Cambiar Contraseña
</a>


    </li>
@endsection
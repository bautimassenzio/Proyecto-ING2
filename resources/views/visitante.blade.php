@extends('layouts.base')

@section('title', 'Bienvenido')

@section('navbar')
<nav class="navbar-visitante">
    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Iniciar Sesión</a>
    <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
</nav>
@endsection

@section('content')
<div class="visitante-container text-center">
    <h1 class="mb-4">¡Bienvenido a Nuestra Aplicación!</h1>
    <p class="lead mb-4">
        Para disfrutar de todas las funcionalidades, por favor inicia sesión o crea una cuenta.
    </p>
    <div>
        <a href="{{ route('login') }}" class="btn btn-lg btn-primary me-3">Iniciar Sesión</a>
        <a href="{{ route('register') }}" class="btn btn-lg btn-outline-primary">Registrarse</a>
    </div>
</div>
@endsection

@section('styles')
<style>
    .visitante-container {
        max-width: 600px;
        margin: 100px auto;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    .navbar-visitante {
        text-align: right;
        padding: 15px 30px;
        background-color: #e9ecef;
        border-bottom: 1px solid #ddd;
    }

    .navbar-visitante a {
        font-weight: 600;
        text-decoration: none;
    }
</style>
@endsection

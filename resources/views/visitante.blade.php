@extends('layouts.base')

@section('title', 'MaquinaTech')

@section('content')
<div class="visitor-simple">
    <div class="auth-container">
        <div class="brand">
            <i class="fas fa-cogs"></i>
            <h1>MaquinaTech</h1>
        </div>
        
        <div class="auth-buttons">
            <a href="{{ route('login') }}" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </a>
            
            <a href="{{ route('register') }}" class="btn btn-register">
                <i class="fas fa-user-plus"></i>
                Registrarse
            </a>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.visitor-simple {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #fff8e1 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.auth-container {
    text-align: center;
    background: white;
    padding: 3rem;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
}

.brand {
    margin-bottom: 2rem;
}

.brand i {
    font-size: 3rem;
    color: #FFB800;
    margin-bottom: 1rem;
}

.brand h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.auth-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-login {
    background-color: #FFB800;
    color: white;
}

.btn-login:hover {
    background-color: #CC9200;
    transform: translateY(-2px);
    color: white;
}

.btn-register {
    background-color: transparent;
    color: #FFB800;
    border: 2px solid #FFB800;
}

.btn-register:hover {
    background-color: #FFB800;
    color: white;
    transform: translateY(-2px);
}

@media (max-width: 480px) {
    .auth-container {
        margin: 1rem;
        padding: 2rem;
    }
}
</style>
@endsection
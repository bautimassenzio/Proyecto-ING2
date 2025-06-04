{{-- resources/views/auth/login.blade.php --}}

@extends('layouts.base') {{-- ¡Asegúrate de que 'layouts.base' sea el nombre correcto de tu archivo de layout! --}}

@section('title', 'Iniciar Sesión') {{-- Esto cambia el título de la pestaña del navegador --}}

{{-- Si quieres elementos de navegación específicos para cuando no hay sesión, puedes definirlos aquí --}}
@section('guest-navigation')
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('login') }}">Login</a>
        </li>
        @if (Route::has('register'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
            </li>
        @endif
    </ul>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        {{-- Quitamos el .login-container y usamos .content-card que ya lo provee el layout --}}
        {{-- <div class="login-container"> --}}

            <h2 class="text-center mb-4">Iniciar Sesión</h2> {{-- Añadimos clases de Bootstrap para centrar y margen --}}

            {{-- Los mensajes de sesión (success/error) y de validación ($errors) ya los maneja el layout en la sección main-content --}}
            {{-- @if(session('error'))
                <div class="error">{{ session('error') }}</div>
            @endif --}}

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3"> {{-- Usamos clases de Bootstrap para espaciado --}}
                    <label for="email" class="form-label">Correo Electrónico</label> {{-- Etiquetas explícitas son mejores para accesibilidad --}}
                    <input
                        id="email" {{-- ID para la etiqueta --}}
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror" {{-- Clases de Bootstrap y de error --}}
                        value="{{ old('email') }}"
                        placeholder="ejemplo@dominio.com" {{-- Placeholder más descriptivo --}}
                        required
                        autocomplete="email"
                        autofocus
                    />
                    @error('email')
                        <div class="invalid-feedback"> {{-- Clase de Bootstrap para mensajes de error --}}
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Contraseña"
                        required
                        autocomplete="current-password"
                    />
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Opcional: Checkbox "Recordarme" si lo necesitas --}}
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Recordarme
                    </label>
                </div>


                <div class="d-grid gap-2"> {{-- Clases de Bootstrap para un botón de ancho completo --}}
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </div>

                {{-- Enlace "¿Olvidaste tu contraseña?" --}}
                @if (Route::has('password.request'))
                    <div class="text-center mt-3">
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                @endif
                {{-- Enlace para registrarse --}}
                <div class="text-center mt-2">
                    ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
                </div>
            </form>
        {{-- </div> --}} {{-- Cierre del .login-container si no lo eliminaste por completo --}}
    </div>
</div>
@endsection

{{-- No necesitas 'additional-styles' aquí, ya que el layout maneja todos los estilos principales --}}
{{-- No necesitas 'additional-scripts' a menos que el login requiera JS muy específico --}}
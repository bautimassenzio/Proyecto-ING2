{{-- resources/views/auth/verify-code.blade.php --}}

@extends('layouts.base') {{-- ¡Asegúrate de que 'layouts.base' sea el nombre correcto de tu archivo de layout! --}}

@section('title', 'Verificación de Código') {{-- Título específico para esta página --}}

{{-- Opcional: Define la navegación si la necesitas, aunque para una página de verificación,
     quizás no haya muchos enlaces de navegación. --}}
@section('navigation')
    {{-- Por ejemplo, si un usuario ya inició sesión pero necesita verificar, o si hay un enlace para volver --}}
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/') }}">Volver al Inicio</a>
    </li>
@endsection

@section('content')
<div class="row justify-content-center"> {{-- Usamos las clases de Bootstrap para centrar el contenido --}}
    <div class="col-md-7 col-lg-5"> {{-- Limitamos el ancho para que la tarjeta se vea bien --}}
        {{-- Aquí se insertará el contenido de la tarjeta de verificación --}}
        <div class="card">
            <div class="card-header text-white text-center bg-primary"> {{-- Usamos bg-primary para el color de fondo --}}
                <h4 class="mb-0">🔐 Verificación de Código</h4>
            </div>

            <div class="card-body p-4">

               

                {{-- Mensaje de éxito (manejo de sesión) --}}
                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
               

                {{-- Formulario de verificación --}}
                <form method="POST" action="{{ route('confirmarAdmin') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Ingrese el código enviado a su correo</label>
                        <input type="text" name="codigo" id="codigo"
                            class="form-control"
                            maxlength="6" required autofocus>

                       
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3"> {{-- Agregamos w-100 y mb-3 --}}
                        ✅ Verificar Código
                    </button>
                </form>

                {{-- Botón para reenviar código --}}
                <form method="POST" action="{{ route('reenviarCodigo') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary w-100"> {{-- Agregamos w-100 --}}
                        🔄 Reenviar código
                    </button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">Este código expirará en 5 minutos.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- No se necesitan scripts adicionales, ya que no hay JS específico en esta vista --}}
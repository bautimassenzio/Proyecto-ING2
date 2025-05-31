<style>
    body {
        background: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
        border-radius: 12px;
    }

    .card-header {
        font-size: 1.2rem;
        background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    }

    .form-label {
        font-weight: 500;
        color: #333;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.1rem rgba(0, 123, 255, 0.25);
    }

    .btn-primary {
        background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
        border: none;
        font-weight: 600;
        border-radius: 8px;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .alert {
        font-size: 0.95rem;
    }

    .alert ul {
        padding-left: 1.2rem;
    }
    button:hover {
  cursor: pointer; /* cambia el cursor a una mano que indica clickeable */
}
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

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

            {{-- Formulario --}}
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-semibold">🔒 Cambiar contraseña</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('passwordReset') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="password_actual" class="form-label">Contraseña actual</label>
                            <input type="password" name="password_actual" id="password_actual"
                                class="form-control @error('password_actual') is-invalid @enderror" required>
                            @error('password_actual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nueva_contraseña" class="form-label">Nueva contraseña</label>
                            <input type="password" name="nueva_contraseña" id="nueva_contraseña"
                                class="form-control @error('nueva_contraseña') is-invalid @enderror" required>
                            @error('nueva_contraseña')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nueva_contraseña_confirmation" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" name="nueva_contraseña_confirmation" id="nueva_contraseña_confirmation"
                                class="form-control @error('nueva_contraseña_confirmation') is-invalid @enderror" required>
                            @error('nueva_contraseña_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            🔁 Actualizar contraseña
                        </button>
                    </form>
                    <form  method="GET" action="{{ route('/') }}">
                        @csrf
                    <button type="submit"" class="btn btn-primary w-100 shadow-sm">
                            Volver a inicio
                    </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

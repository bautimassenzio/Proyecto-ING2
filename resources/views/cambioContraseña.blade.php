@extends($layout)

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">



            {{-- Formulario --}}
            <div class="card shadow-sm border-0">
                <div class="card-header text-white fw-semibold">🔒 Cambiar contraseña</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('passwordReset') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="password_actual" class="form-label">Contraseña actual</label>
                            <input type="password" name="password_actual" id="password_actual"
                                class="form-control"  required>
                            
                        </div>

                        <div class="mb-3">
                            <label for="nueva_contraseña" class="form-label">Nueva contraseña</label>
                            <input type="password" name="nueva_contraseña" id="nueva_contraseña"
                            class="form-control"  required>
                        </div>

                        <div class="mb-3">
                            <label for="nueva_contraseña_confirmation" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" name="nueva_contraseña_confirmation" id="nueva_contraseña_confirmation"
                            class="form-control"  required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 shadow-sm mb-2">
                            🔁 Actualizar contraseña
                        </button>
                    </form>

                    <form method="GET" action="{{ route('/') }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary w-100 shadow-sm">
                            Volver a inicio
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

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
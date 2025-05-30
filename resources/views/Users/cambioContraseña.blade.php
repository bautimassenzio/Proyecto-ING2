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
            <div class="card shadow-sm border-0 rounded">
                <div class="card-header bg-primary text-white fw-semibold">🔒 Cambiar contraseña</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('passwordReset') }}">
                        @csrf

                        {{-- Contraseña actual --}}
                        <div class="mb-3">
                            <label for="password_actual" class="form-label">Contraseña actual</label>
                            <input type="password" name="password_actual" id="password_actual"
                                class="form-control @error('password_actual') is-invalid @enderror" required>
                            @error('password_actual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nueva contraseña --}}
                        <div class="mb-3">
                            <label for="nueva_contraseña" class="form-label">Nueva contraseña</label>
                            <input type="password" name="nueva_contraseña" id="nueva_contraseña"
                                class="form-control @error('nueva_contraseña') is-invalid @enderror" required>
                            @error('nueva_contraseña')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirmar nueva contraseña --}}
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
                </div>
            </div>

        </div>
    </div>
</div>
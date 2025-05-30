@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-6">
        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white text-center rounded-top-4">
                <h4 class="mb-0">Verificación de Código</h4>
            </div>

            <div class="card-body p-4">
                {{-- Mensaje de error --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                {{-- Formulario --}}
                <form method="POST" action="{{ route('confirmarAdmin') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Ingrese el código enviado a su correo</label>
                        <input type="text" name="codigo" id="codigo"
                            class="form-control @error('codigo') is-invalid @enderror"
                            maxlength="6" required autofocus>

                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Verificar Código
                    </button>
                </form>

                <form method="POST" action="{{ route('reenviarCodigo') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        Reenviar código
                    </button>
                </form>


                <div class="text-center mt-3">
                    <small class="text-muted">Este código expirará en 5 minutos.</small>
                </div>
            </div>
        </div>
    </div>
</div>
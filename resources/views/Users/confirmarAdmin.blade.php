<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de Código</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f2f5fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }

        .card-header {
            font-size: 1.25rem;
            font-weight: 600;
            background: linear-gradient(90deg, #007bff, #0056b3);
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(90deg, #007bff, #0056b3);
            border: none;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px;
        }

        .btn-secondary {
            margin-top: 10px;
            background-color: #6c757d;
            font-weight: 500;
            border-radius: 10px;
            width: 100%;
        }

        .alert {
            font-size: 0.95rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="card">
        <div class="card-header text-white text-center">
            <h4 class="mb-0">🔐 Verificación de Código</h4>
        </div>

        <div class="card-body p-4">

            {{-- Mensajes de error --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulario de verificación --}}
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
                    ✅ Verificar Código
                </button>
            </form>

            {{-- Botón para reenviar código --}}
            <form method="POST" action="{{ route('reenviarCodigo') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    🔄 Reenviar código
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">Este código expirará en 5 minutos.</small>
            </div>
        </div>
    </div>
</div>

</body>
</html>
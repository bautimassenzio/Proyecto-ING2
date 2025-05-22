<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 320px;
        }
        h2 {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.6rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 0.7rem;
            width: 100%;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <input
            type="email"
            name="email"
            placeholder="Correo electrónico"
            value="{{ old('email') }}"
            required
            autofocus
        />
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <input
            type="password"
            name="password"
            placeholder="Contraseña"
            required
        />
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit">Entrar</button>
    </form>
</div>

</body>
</html>

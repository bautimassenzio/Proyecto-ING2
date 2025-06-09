<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Código de Verificación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .codigo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hola {{ $nombreCliente }},</h2>

        <p>Recibimos un intento de inicio de sesión como administrador.</p>
        <p>Tu código de verificación es:</p>

        <p class="codigo">{{ $contraseña }}</p>

        <p>Este código expirará en 5 minutos. Si no solicitaste este inicio de sesión, podés ignorar este mensaje.</p>

        <div class="footer">
            <p>Este mensaje fue generado automáticamente. Por favor, no respondas a este correo.</p>
        </div>
    </div>
</body>
</html>

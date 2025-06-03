<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reserva</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background-color: #f8f8f8; padding: 10px; text-align: center; border-bottom: 1px solid #eee; }
        .content { margin-top: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 0.9em; color: #777; }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff !important; /* !important para anular estilos del cliente de correo */
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .details-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>¡Tu reserva ha sido confirmada!</h2>
        </div>
        <div class="content">
            <p>Hola, {{ $nombreCliente ?? 'Cliente' }},</p>
            <p>Gracias por tu reserva. A continuación, los detalles de tu confirmación:</p>

            <table class="details-table">
                <tr>
                    <th>ID de Reserva:</th>
                    <td>{{ $idReserva }}</td>
                </tr>
                <tr>
                    <th>Máquina Reservada:</th>
                    {{-- *** NUEVA LÍNEA CLAVE: Accediendo a la maquinaria *** --}}
                    <td>{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }} ({{ $reserva->maquinaria->numero_inventario }})</td>
                </tr>
                <tr>
                    <th>Fechas:</th>
                    <td>Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Total a Pagar:</th>
                    <td>${{ number_format($pagoTotal, 2, ',', '.') }}</td>
                </tr>
                {{-- Puedes agregar más detalles de la maquinaria si los necesitas --}}
                {{-- <tr>
                    <th>Ubicación:</th>
                    <td>{{ $reserva->maquinaria->localidad }}</td>
                </tr> --}}
            </table>

            <p>Si tienes alguna pregunta o necesitas modificar tu reserva, no dudes en contactarnos.</p>
            <p>Saludos cordiales,</p>
            <p>El equipo de [ManyMaquinarias]</p>

            <p style="text-align: center; margin-top: 25px;">
                <a href="{{ url('/') }}" class="button">Visita nuestro sitio web</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} [ManyMaquinarias]. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
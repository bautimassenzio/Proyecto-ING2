<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reserva</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
        .header { background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; padding: 10px; font-size: 0.8em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/Manny_Maquinarias_loco.png') }}" alt="Logo de ManyMaquinarias" style="max-height: 60px; margin-bottom: 10px;">
            <h2>¡Tu Reserva Ha Sido Confirmada!</h2>
        </div>
        <div class="content">
            <p>Hola **{{ $nombreCliente }}**,</p>
            <p>Nos complace informarte que tu reserva ha sido confirmada con éxito.</p>
            <p><strong>Detalles de tu reserva:</strong></p>
            <ul>
                <li><strong>Número de Reserva:</strong> #{{ $idReserva }}</li>
                <li><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</li>
                <li><strong>Fecha de Fin:</strong> {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</li>
                <li><strong>Pago Total Estimado:</strong> ${{ number_format($pagoTotal, 2, ',', '.') }}</li>
                {{-- <li><strong>Maquinaria Reservada:</strong> [Nombre de la Maquinaria]</li> --}}
            </ul>
            <p>En breve nos pondremos en contacto contigo para coordinar los detalles de la entrega.</p>
            <p>¡Gracias por elegirnos!</p>
            <p>Saludos cordiales,</p>
            <p>El Equipo de ManyMaquinarias</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} ManyMaquinarias. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
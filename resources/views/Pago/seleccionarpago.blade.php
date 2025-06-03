<!DOCTYPE html>
<html>
<head>
    <title>Seleccionar método de pago</title>
</head>
<body>
    <h1>Seleccione método de pago</h1>

    <form action="{{ route('pago.procesar') }}" method="POST">
        @csrf
        <button type="submit">Pagar con Mercado Pago</button>
    </form>

    <button onclick="alert('Funcionalidad aún no implementada')">Pagar con Tarjeta</button>
</body>
</html>
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

    <form action="{{ route('pago.procesar.tarjeta') }}" method="GET">
        <button type="submit">Pagar con Tarjeta</button>
    </form>
</body>
</html>
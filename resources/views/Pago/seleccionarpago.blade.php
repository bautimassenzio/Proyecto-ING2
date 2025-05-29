<!DOCTYPE html>
<html>
<head>
    <title>Seleccionar método de pago</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .payment-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 400px;
            max-width: 95%;
            text-align: center;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .payment-option {
            margin-bottom: 20px;
        }

        .payment-option button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 14px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            transition: background-color 0.2s ease-in-out;
        }

        .payment-option button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .payment-option:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Seleccione método de pago</h1>

        <div class="payment-option">
            <form action="{{ route('pago.procesar') }}" method="POST">
                @csrf
                <button type="submit">Pagar con Mercado Pago</button>
            </form>
        </div>

        <div class="payment-option">
            <form action="{{ route('pago.procesar.tarjeta') }}" method="GET">
                <button type="submit">Pagar con Tarjeta</button>
            </form>
        </div>
    </div>
</body>
</html>
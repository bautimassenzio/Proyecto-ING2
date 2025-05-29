<!DOCTYPE html>
<html>
<head>
    <title>Pago con Tarjeta</title>
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

        .card-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 400px;
            max-width: 95%;
        }

        .card-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .card-header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 20px;
            font-size: 14px;
            padding: 10px;
            background-color: #fdecea;
            border: 1px solid #f9c9d3;
            border-radius: 6px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 15px;
        }

        .form-group input[type="text"],
        .form-group select {
            width: calc(100% - 16px);
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            color: #333;
        }

        .form-group select {
            appearance: none;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill="%23555" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 1.2em;
            padding-right: 35px;
        }

        .form-group input[type="text"]:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        .form-actions {
            margin-top: 30px;
        }

        .form-actions button[type="submit"],
        .form-actions .back-link {
            background-color: #007bff;
            color: white;
            padding: 14px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            display: block;
            text-align: center;
            text-decoration: none;
            box-sizing: border-box;
            transition: background-color 0.2s ease-in-out;
        }

        .form-actions button[type="submit"]:hover,
        .form-actions .back-link:hover {
            background-color: #0056b3;
        }

        .form-actions .back-link {
            margin-top: 15px;
            background-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="card-container">
        <div class="card-header">
            <h1>Pago con Tarjeta</h1>
        </div>

        @if ($errors->any())
            <div class="error-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('procesar.pago.tarjeta') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="card_number">Número de Tarjeta:</label>
                <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX"
                       pattern="^\d{4} \d{4} \d{4} \d{4}$"
                       title="Ingrese el número de tarjeta en el formato XXXX XXXX XXXX XXXX"
                       required>
            </div>

            <div class="form-group">
                <label for="expiry_month">Mes de Vencimiento:</label>
                <select id="expiry_month" name="expiry_month" required>
                    <option value="">Seleccionar Mes</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label for="expiry_year">Año de Vencimiento:</label>
                <select id="expiry_year" name="expiry_year" required>
                    <option value="">Seleccionar Año</option>
                    @php
                        $currentYear = intval(date('y'));
                        for ($i = 0; $i <= 10; $i++) {
                            $year = $currentYear + $i;
                            echo "<option value='".sprintf('%02d', $year)."'>20".sprintf('%02d', $year)."</option>";
                        }
                    @endphp
                </select>
            </div>

            <div class="form-group">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" placeholder="XXX o XXXX" required>
            </div>

            <div class="form-actions">
                <button type="submit">Pagar</button>
                <a href="{{ route('pago.seleccionar') }}" class="back-link">Volver</a>
            </div>
        </form>
    </div>
</body>
</html>
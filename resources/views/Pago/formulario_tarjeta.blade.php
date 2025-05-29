<!DOCTYPE html>
<html>
<head>
    <title>Pago con Tarjeta</title>
    <style>
        body {
            font-family: sans-serif;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Ingrese los datos de su tarjeta</h1>

    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form  method="POST">
        @csrf

        <div class="form-group">
            <label for="card_number">Número de Tarjeta:</label>
            <input type="text" id="card_number" name="card_number" placeholder="XXXX-XXXX-XXXX-XXXX" required>
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
                    $currentYear = date('y');
                    for ($i = 0; $i <= 10; $i++) {
                        $year = $currentYear + $i;
                        echo "<option value='$year'>20$year</option>";
                    }
                @endphp
            </select>
        </div>

        <div class="form-group">
            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" placeholder="XXX o XXXX" required>
        </div>

        <button type="submit">Pagar</button>
        
    </form>
</body>
</html>
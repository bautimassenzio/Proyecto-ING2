{{-- resources/views/admin/statistics/new-clients-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Estadísticas de Nuevos Clientes</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            color: #333;
        }
        h1 {
            color: #4a4a4a;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        .summary {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .summary p {
            margin: 5px 0;
            font-size: 16px;
        }
        .summary .count {
            font-size: 36px;
            font-weight: bold;
            color: #6a0dad; /* Color morado */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #555;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <h1>Estadísticas de Nuevos Clientes</h1>

    <div class="summary">
        <p>Período de consulta:</p>
        <p>Desde el <strong>{{ $fechaInicio }}</strong> hasta el <strong>{{ $fechaFin }}</strong></p>
        <p>Total de nuevos clientes registrados:</p>
        <p class="count">{{ $nuevosClientesCount }}</p>
    </div>

    <h2>Tendencia de Nuevos Clientes ({{ $periodType === 'month' ? 'Por Mes' : 'Por Semana' }})</h2>

    @if(count($chartData) > 0)
        <table>
            <thead>
                <tr>
                    <th>{{ $periodType === 'month' ? 'Mes y Año' : 'Semana del Año' }}</th>
                    <th>Cantidad de Clientes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chartLabels as $index => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $chartData[$index] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay datos suficientes para mostrar la tendencia de nuevos clientes en el período seleccionado.</p>
    @endif

    <div class="footer">
        Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
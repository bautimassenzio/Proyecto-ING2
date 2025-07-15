{{-- resources/views/admin/statistics/income-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Estadísticas de Ingresos</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            color: #333;
        }
        h1 {
            color: #4a4a4a;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }
        .summary {
            background-color: #e6f7ff; /* Un azul claro */
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 5px solid #3498db; /* Borde azul */
        }
        .summary p {
            margin: 5px 0;
            font-size: 16px;
        }
        .summary .total {
            font-size: 32px;
            font-weight: bold;
            color: #27ae60; /* Verde para el monto */
            margin-top: 10px;
        }
        .chart-section {
            margin-top: 30px;
        }
        .chart-section h2 {
            font-size: 20px;
            color: #555;
            margin-bottom: 15px;
            text-align: center;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f2f2f2;
            color: #555;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <h1>Estadísticas de Ingresos</h1>

    <div class="summary">
        <p>Período de consulta:</p>
        <p>Desde el <strong>{{ $fechaInicio }}</strong> hasta el <strong>{{ $fechaFin }}</strong></p>
        <p class="total">Total de Ingresos: ${{ number_format($totalIncome, 2, ',', '.') }}</p>
    </div>

    <div class="chart-section">
        <h2>Tendencia de Ingresos ({{ $periodType === 'month' ? 'Por Mes' : 'Por Semana' }})</h2>
        @if(count($chartLabels) > 0 && count($chartData) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>Ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chartLabels as $index => $label)
                        <tr>
                            <td>{{ $label }}</td>
                            <td>${{ number_format($chartData[$index], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="text-align: center; margin-top: 20px; font-style: italic; color: #666;">
                Nota: Los gráficos visuales no pueden ser generados directamente en el PDF de esta forma. Aquí se muestra la tabla de datos.
            </p>
        @else
            <p style="text-align: center; font-style: italic; color: #666;">No hay datos de ingresos para mostrar la tendencia en este período.</p>
        @endif
    </div>

    <div class="footer">
        Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
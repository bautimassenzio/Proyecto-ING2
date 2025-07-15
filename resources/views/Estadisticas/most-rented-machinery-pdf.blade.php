{{-- resources/views/admin/statistics/most-rented-machinery-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Estadísticas de Maquinarias Más Alquiladas</title>
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
    <h1>Estadísticas de Maquinarias Más Alquiladas</h1>

    <div class="summary">
        <p>Período de consulta:</p>
        <p>Desde el <strong>{{ $fechaInicio }}</strong> hasta el <strong>{{ $fechaFin }}</strong></p>
    </div>

    <h2>Top 10 Maquinarias Más Alquiladas</h2>

    @if(count($mostRentedMachinery) > 0)
        <table>
            <thead>
                <tr>
                    <th>Nombre de la Maquinaria</th>
                    <th>Cantidad de Alquileres</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mostRentedMachinery as $item)
                    <tr>
                        <td>{{ $item['nombre'] }}</td>
                        <td>{{ $item['cantidad_alquileres'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay datos suficientes para mostrar las maquinarias más alquiladas en el período seleccionado.</p>
    @endif

    <div class="footer">
        Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
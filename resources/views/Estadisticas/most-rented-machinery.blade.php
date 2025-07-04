@extends($layout)

@section('title', 'Estadísticas - Maquinarias Más Alquiladas')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Estadísticas: Maquinarias Más Alquiladas</h1>

    <div class="grid grid-cols-1 gap-6">
        <!-- Tarjeta: Maquinarias Más Alquiladas por Período -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Maquinarias Más Alquiladas</h2>
            
            <form action="{{ route('admin.estadisticas.maquinas-mas-alquiladas') }}" method="GET" class="mb-4">
                <div class="mb-3">
                    <label for="fecha_inicio" class="block text-gray-700 text-sm font-bold mb-2">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           value="{{ request('fecha_inicio', \Carbon\Carbon::now()->subMonths(12)->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="block text-gray-700 text-sm font-bold mb-2">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           value="{{ request('fecha_fin', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) }}">
                </div>
                
                <button type="submit" class="btn-primary-small">
                    Consultar
                </button>
            </form>

            <div class="mt-4">
                @isset($mostRentedMachinery)
                    @if(count($mostRentedMachinery) > 0)
                        <p class="text-md text-gray-600 mb-2">Top 10 maquinarias más alquiladas en el período:</p>
                        <ul class="list-disc list-inside">
                            @foreach($mostRentedMachinery as $machinery)
                                <li class="text-gray-700">
                                    <strong>{{ $machinery['nombre'] }}</strong>: {{ $machinery['cantidad_alquileres'] }} alquileres
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-md text-gray-600 italic">No se encontraron alquileres de maquinarias en este período.</p>
                    @endif
                @else
                    <p class="text-md text-gray-600">Selecciona un período para consultar las maquinarias más alquiladas.</p>
                @endisset
            </div>
        </div>
    </div>

    <!-- Sección del Gráfico -->
    @if(count($chartData) > 0)
    <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Ranking de Maquinarias Más Alquiladas</h2>
        <canvas id="mostRentedMachineryChart" style="max-height: 400px;"></canvas>
    </div>
    @else
    <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
        <p class="text-md text-gray-600 italic">No hay datos suficientes para mostrar el gráfico de maquinarias más alquiladas en el período seleccionado.</p>
    </div>
    @endif

</div>

@endsection

@push('scripts')
{{-- Incluir Chart.js desde un CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos pasados desde Laravel
        const chartLabels = @json($chartLabels ?? []);
        const chartData = @json($chartData ?? []);

        if (chartLabels.length > 0 && chartData.length > 0) {
            const ctx = document.getElementById('mostRentedMachineryChart').getContext('2d');
            // Destruir cualquier instancia anterior del gráfico
            if (window.mostRentedMachineryChartInstance) {
                window.mostRentedMachineryChartInstance.destroy();
            }

            window.mostRentedMachineryChartInstance = new Chart(ctx, {
                type: 'bar', // Gráfico de barras
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Cantidad de Alquileres',
                        data: chartData,
                        backgroundColor: 'rgba(255, 159, 64, 0.6)', // Color naranja
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // Hace las barras horizontales
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de Alquileres'
                            },
                            ticks: {
                                precision: 0 // Asegura que los ticks sean números enteros
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Maquinaria'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false, // No es necesario la leyenda para un solo dataset
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        } else if (window.mostRentedMachineryChartInstance) {
            window.mostRentedMachineryChartInstance.destroy();
            window.mostRentedMachineryChartInstance = null;
        }
    });
</script>
@endpush

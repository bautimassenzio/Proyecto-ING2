@extends($layout)

@section('title', 'Estadísticas - Nuevos Clientes')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Estadísticas: Nuevos Clientes Registrados</h1>

    <div class="grid grid-cols-1 gap-6">
        <!-- Tarjeta: Clientes Registrados por Período -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Consulta de Nuevos Clientes</h2>
            
            <form action="{{ route('admin.estadisticas.nuevos-clientes') }}" method="GET" class="mb-4">
                <div class="mb-3">
                    <label for="fecha_inicio" class="block text-gray-700 text-sm font-bold mb-2">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           value="{{ request('fecha_inicio', \Carbon\Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="block text-gray-700 text-sm font-bold mb-2">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           value="{{ request('fecha_fin', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) }}">
                </div>
                
                {{-- Selector para el tipo de período --}}
                <div class="mb-3">
                    <label for="period_type" class="block text-gray-700 text-sm font-bold mb-2">Mostrar por:</label>
                    <select id="period_type" name="period_type" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="month" {{ ($periodType ?? 'month') === 'month' ? 'selected' : '' }}>Mes</option>
                        <option value="week" {{ ($periodType ?? 'month') === 'week' ? 'selected' : '' }}>Semana</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary-small"> {{-- Clase de estilo para el botón --}}
                    Consultar
                </button>
            </form>

            <div class="mt-4">
                @isset($nuevosClientesCount)
                    @if($nuevosClientesCount > 0)
                        <p class="text-4xl font-bold text-purple-600">{{ $nuevosClientesCount }}</p>
                        <p class="text-sm text-gray-500 mt-2">Nuevos clientes registrados en el período seleccionado.</p>
                    @else
                        <p class="text-md text-gray-600 italic">No se registraron nuevos clientes en este período.</p>
                    @endif
                @else
                    <p class="text-md text-gray-600">Selecciona un período para consultar nuevos clientes.</p>
                @endisset
            </div>
        </div>
    </div>

    <!-- Sección del Gráfico -->
    @if(count($chartData) > 0)
    <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Tendencia de Nuevos Clientes ({{ ($periodType ?? 'month') === 'month' ? 'Por Mes' : 'Por Semana' }})</h2>
        <canvas id="newClientsChart" style="max-height: 400px;"></canvas>
    </div>
    @else
    <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
        <p class="text-md text-gray-600 italic">No hay datos suficientes para mostrar el gráfico de nuevos clientes en el período seleccionado.</p>
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
        const periodType = @json($periodType ?? 'month'); // Obtener el tipo de período seleccionado

        // Texto dinámico para el título del eje X
        const xAxisTitle = periodType === 'month' ? 'Mes y Año' : 'Semana del Año';

        // Solo inicializa el gráfico si hay datos
        if (chartLabels.length > 0 && chartData.length > 0) {
            const ctx = document.getElementById('newClientsChart').getContext('2d');
            // Asegurarse de destruir cualquier instancia anterior del gráfico para evitar duplicados
            if (window.newClientsChartInstance) {
                window.newClientsChartInstance.destroy();
            }

            window.newClientsChartInstance = new Chart(ctx, { // Almacenar la instancia en el objeto window
                type: 'bar', // Tipo de gráfico de barras
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Nuevos Clientes',
                        data: chartData,
                        backgroundColor: 'rgba(153, 102, 255, 0.6)', // Color púrpura suave
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Permite que el gráfico se ajuste a su contenedor
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de Clientes'
                            },
                            ticks: {
                                precision: 0 // Asegura que los ticks sean números enteros
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: xAxisTitle // Título del eje X dinámico
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
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
        } else if (window.newClientsChartInstance) {
            // Si no hay datos y ya existía un gráfico, destrúyelo para limpiar la vista
            window.newClientsChartInstance.destroy();
            window.newClientsChartInstance = null;
        }
    });
</script>
@endpush

@extends($layout)

@section('title', 'Estadísticas - Ingresos')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Estadísticas: Ingresos del Sistema</h1>

    <div class="grid grid-cols-1 gap-6">
        <!-- Tarjeta: Ingresos Totales por Período -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Ingresos por Período</h2>
            
            <form action="{{ route('admin.estadisticas.ingresos') }}" method="GET" class="mb-4">
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

                <button type="submit" class="btn-primary-small">
                    Consultar
                </button>
            </form>

            <div class="mt-4">
                @isset($totalIncome)
                    @if($totalIncome > 0)
                        <p class="text-4xl font-bold text-green-600">${{ number_format($totalIncome, 2, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 mt-2">Ingresos totales en el período seleccionado.</p>
                    @else
                        <p class="text-md text-gray-600 italic">No se registraron ingresos en este período.</p>
                    @endif
                @else
                    <p class="text-md text-gray-600">Selecciona un período para consultar los ingresos.</p>
                @endisset
            </div>
        </div>
    </div>

    <!-- Sección del Gráfico -->
    @if(count($chartData) > 0)
    <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Tendencia de Ingresos ({{ ($periodType ?? 'month') === 'month' ? 'Por Mes' : 'Por Semana' }})</h2>
        <canvas id="incomeChart" style="max-height: 400px;"></canvas>
    </div>
    @else
    <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
        <p class="text-md text-gray-600 italic">No hay datos suficientes para mostrar el gráfico de ingresos en el período seleccionado.</p>
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
        const periodType = @json($periodType ?? 'month');

        const xAxisTitle = periodType === 'month' ? 'Mes y Año' : 'Semana del Año';

        if (chartLabels.length > 0 && chartData.length > 0) {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            // Destruir cualquier instancia anterior del gráfico
            if (window.incomeChartInstance) {
                window.incomeChartInstance.destroy();
            }

            window.incomeChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Ingresos',
                        data: chartData,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)', // Color verde-azulado
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto de Ingresos ($)'
                            },
                            ticks: {
                                callback: function(value, index, values) {
                                    return '$' + value.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: xAxisTitle
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
                                    return context.dataset.label + ': $' + context.raw.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        } else if (window.incomeChartInstance) {
            window.incomeChartInstance.destroy();
            window.incomeChartInstance = null;
        }
    });
</script>
@endpush

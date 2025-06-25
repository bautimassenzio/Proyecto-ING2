@extends($layout)

@section('title', 'Estadísticas del Sistema')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Estadísticas del Sistema</h1>

    <div class="grid grid-cols-1 gap-6">
        <!-- Tarjeta: Clientes Registrados por Período -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Clientes Registrados por Período</h2>
            
            <form action="{{ route('admin.estadisticas') }}" method="GET" class="mb-4">
                <div class="mb-3">
                    <label for="fecha_inicio" class="block text-gray-700 text-sm font-bold mb-2">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           value="{{ request('fecha_inicio', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="block text-gray-700 text-sm font-bold mb-2">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           value="{{ request('fecha_fin', \Carbon\Carbon::now()->format('Y-m-d')) }}">
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
</div>
@endsection

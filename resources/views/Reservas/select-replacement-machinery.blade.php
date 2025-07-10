@extends('layouts.base')

@section('content')
<div class="bg-gray-100 min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-4xl mx-auto mt-8 mb-8 border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manejar Maquinaria Inactiva para Reserva #{{ $reserva->id_reserva }}</h1>
            <a href="{{ route('reservas.listas-para-entregar') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                Volver a Reservas para Entregar
            </a>
        </div>

        {{-- Mensajes de éxito/error de sesión --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <p class="font-bold">¡Atención!</p>
            <p>La maquinaria original asignada a esta reserva (ID: {{ $originalMachinery->id_maquinaria }} - {{ $originalMachinery->marca }} {{ $originalMachinery->modelo }}) está actualmente <span class="font-bold">INACTIVA</span>.</p>
            <p>Por favor, seleccione una maquinaria de reemplazo de la lista de abajo o cancele la reserva.</p>
        </div>

        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Detalles de la Reserva Original:</h2>
        <div class="mb-6 p-4 border border-gray-200 rounded-md bg-gray-50">
            <p><strong>Cliente:</strong> {{ $reserva->cliente->nombre ?? 'N/A' }}</p>
            <p><strong>Maquinaria Original:</strong> {{ $originalMachinery->marca }} {{ $originalMachinery->modelo }} (Nro. Inventario: {{ $originalMachinery->nro_inventario }})</p>
            <p><strong>Tipo de Uso:</strong> {{ $originalMachinery->tipo_uso }}</p>
            <p><strong>Localidad:</strong> {{ $originalMachinery->localidad }}</p>
            <p><strong>Fecha Inicio:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</p>
            <p><strong>Fecha Fin:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</p>
            <p><strong>Total Reserva:</strong> ${{ number_format($reserva->total, 2) }}</p>
        </div>

        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Maquinarias Similares Disponibles para Intercambio:</h2>

        @if ($similarAvailableMachinery->isEmpty())
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">No se encontraron maquinarias similares disponibles en la misma localidad.</span>
            </div>
        @else
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">ID</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Nro. Inventario</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Marca / Modelo</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Costo/Día</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($similarAvailableMachinery as $machinery)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ $machinery->id_maquinaria }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ $machinery->nro_inventario }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ $machinery->marca }} {{ $machinery->modelo }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ $machinery->uso }} {{ $machinery->uso }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">${{ number_format($machinery->precio_dia, 2) }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">
                                    <form action="{{ route('reservas.swap-machinery', $reserva->id_reserva) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="new_maquinaria_id" value="{{ $machinery->id_maquinaria }}">
                                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 text-xs w-full">
                                            Seleccionar para Intercambio
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Otras Opciones:</h2>
        <div class="mb-6 p-4 border border-gray-200 rounded-md bg-gray-50">
            <p class="text-gray-700 mb-3">Si el cliente no desea una maquinaria de reemplazo, puede cancelar la reserva:</p>
            <form action="{{ route('reservas.cancel-and-refund', $reserva->id_reserva) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" onclick="return confirm('¿Está seguro de cancelar esta reserva y reembolsar el monto total al cliente? Esta acción es irreversible.')"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200 w-full">
                    Cancelar Reserva y Reembolsar Monto Total
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
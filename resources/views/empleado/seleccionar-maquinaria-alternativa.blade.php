@extends($layout)

@section('title', 'Seleccionar Maquinaria Alternativa')

@section('content')
<div class="container mx-auto p-4">
    {{-- IDENTIFICADOR ÚNICO DE VERSIÓN: V20240710-SOLUCION-DIRECTA --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Seleccionar Maquinaria Alternativa</h1>

    {{-- Mensajes de éxito o error --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-yellow-500">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Detalles de la Reserva</h2>
        <p class="text-gray-700 mb-2"><strong>ID Reserva:</strong> {{ $reserva->id_reserva }}</p>
        <p class="text-gray-700 mb-2"><strong>Cliente:</strong> {{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</p>
        <p class="text-gray-700 mb-2"><strong>Maquinaria Original:</strong> {{ $maquinariaOriginal->marca }} {{ $maquinariaOriginal->modelo }} (Estado: <span class="font-bold text-red-600">{{ $maquinariaOriginal->estado }}</span>)</p>
        <p class="text-gray-700 mb-2"><strong>Período de Alquiler:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</p>
        <p class="text-gray-700 mb-2"><strong>Localidad:</strong> {{ $maquinariaOriginal->localidad->nombre ?? 'N/A' }}</p>
    </div>

    @if($maquinariasAlternativas->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <p class="text-gray-600 italic">No hay maquinarias alternativas disponibles en la misma localidad y del mismo tipo.</p>
            <div class="mt-4 flex justify-end">
                {{-- Botón Cancelar Reserva (ahora un enlace GET directo) --}}
                <a href="{{ route('reservas.cancelar-directo', $reserva->id_reserva) }}"
                   onclick="return confirm('¿Estás seguro de cancelar esta reserva directamente? Esta acción no se puede deshacer.');"
                   class="btn-primary-small bg-red-500 hover:bg-red-600 mr-2">
                    Cancelar Reserva
                </a>
                <a href="{{ route('empleado.panel-entregas-devoluciones') }}" class="btn-primary-small bg-gray-500 hover:bg-gray-600">Volver al Panel</a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Maquinarias Alternativas Disponibles en {{ $maquinariaOriginal->localidad->nombre ?? 'esta localidad' }} (Mismo Tipo)</h2>
            <p class="text-gray-600 mb-4">Selecciona una maquinaria de la lista para completar la entrega o cancela la reserva.</p>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 border-b border-gray-200">Maquinaria</th>
                            <th class="py-3 px-6 border-b border-gray-200">Nro. Inventario</th>
                            <th class="py-3 px-6 border-b border-gray-200">Precio/Día</th>
                            <th class="py-3 px-6 border-b border-gray-200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @foreach($maquinariasAlternativas as $maquinaria)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6">{{ $maquinaria->marca }} {{ $maquinaria->modelo }}</td>
                                <td class="py-3 px-6">{{ $maquinaria->nro_inventario }}</td>
                                <td class="py-3 px-6">${{ number_format($maquinaria->precio_dia, 2, ',', '.') }}</td>
                                <td class="py-3 px-6">
                                    <form action="{{ route('reservas.procesar-entrega-alternativa', $reserva->id_reserva) }}" method="POST" onsubmit="return confirm('¿Estás seguro de entregar esta maquinaria alternativa?');">
                                        @csrf
                                        @method('PUT') {{-- Usamos PUT para la actualización --}}
                                        <input type="hidden" name="id_maquinaria_alternativa" value="{{ $maquinaria->id_maquinaria }}">
                                        <button type="submit" class="btn-primary-small">Seleccionar y Entregar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-right">
                {{-- Botón Cancelar Reserva (ahora un enlace GET directo) --}}
                <a href="{{ route('reservas.cancelar-directo', $reserva->id_reserva) }}"
                   onclick="return confirm('¿Estás seguro de cancelar esta reserva directamente? Esta acción no se puede deshacer.');"
                   class="btn-primary-small bg-red-500 hover:bg-red-600 mr-2">
                    Cancelar Reserva
                </a>
                <a href="{{ route('empleado.panel-entregas-devoluciones') }}" class="btn-primary-small bg-gray-500 hover:bg-gray-600">Volver al Panel</a>
            </div>
        </div>
    @endif
</div>
@endsection

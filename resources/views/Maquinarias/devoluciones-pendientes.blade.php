@extends('layouts.base')

@section('content')
<div class="bg-gray-100 min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-6xl mx-auto mt-8 mb-8 border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Maquinarias con Devolución Pendiente y Registro</h1>
            <a href="{{ route('catalogo.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                Volver al Catálogo
            </a>
        </div>


        {{-- Comprobación si hay maquinarias para mostrar --}}
        @if ($maquinariasConDevolucionPendiente->isEmpty())
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">No hay maquinarias con devoluciones pendientes o registradas actualmente.</span>
            </div>
        @else
            {{-- Tabla de maquinarias --}}
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">ID Maquinaria</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Nro. Inventario</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Marca / Modelo</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Reservas (Pendientes/Registradas)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($maquinariasConDevolucionPendiente as $maquinaria)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ $maquinaria->id_maquinaria }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ $maquinaria->nro_inventario }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ $maquinaria->marca }} {{ $maquinaria->modelo }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">
                                    {{-- Iterar sobre las reservas --}}
                                    @foreach ($maquinaria->reservas as $reserva)
                                        <div class="mb-2 p-2 border rounded-md
                                            {{ $reserva->estado === 'finalizada' ? 'border-gray-300 bg-gray-100' : 'border-gray-200 bg-gray-50' }}">
                                            <p><strong>Reserva ID:</strong> {{ $reserva->id_reserva }}</p>
                                            <p><strong>Cliente:</strong> {{ $reserva->cliente->nombre ?? 'N/A' }}</p>
                                            <p><strong>Inicio:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</p>
                                            <p><strong>Fin:</strong> <span class="font-bold text-red-600">{{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</span></p>
                                            <p><strong>Estado:</strong>
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    {{ $reserva->estado === 'aprobada' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $reserva->estado === 'activa' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $reserva->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $reserva->estado === 'finalizada' ? 'bg-gray-300 text-gray-800' : '' }}
                                                    {{ $reserva->estado === 'cancelada' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ $reserva->estado }}
                                                    @if ($reserva->estado === 'finalizada')
                                                        (Devuelta)
                                                    @endif
                                                </span>
                                            </p>

                                            {{-- Botón para registrar la devolución (solo si la reserva no está finalizada/cancelada) --}}
                                            @if ($reserva->estado !== 'finalizada' && $reserva->estado !== 'cancelada')
                                                <form action="{{ route('reservas.registrar-devolucion', $reserva->id_reserva) }}" method="POST" class="mt-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-200 text-xs w-full">
                                                        Registrar Devolución
                                                    </button>
                                                </form>
                                            @else
                                                <p class="text-xs text-gray-500 mt-2">Acción no disponible para estado {{ $reserva->estado }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
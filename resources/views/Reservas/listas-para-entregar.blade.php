@extends('layouts.base')

@section('content')
<div class="bg-gray-100 min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-6xl mx-auto mt-8 mb-8 border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Reservas Aprobadas para Entregar</h1> {{-- Título ajustado --}}
            <a href="{{ route('catalogo.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                Volver al Catálogo
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

        {{-- Comprobación si hay reservas para mostrar --}}
        @if ($reservasListasParaEntregar->isEmpty())
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">No hay reservas aprobadas para entregar actualmente.</span>
            </div>
        @else
            {{-- Tabla de reservas --}}
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">ID Reserva</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Maquinaria</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Cliente</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Fecha Inicio</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Fecha Fin</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Estado</th>
                            <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasListasParaEntregar as $reserva)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ $reserva->id_reserva }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">
                                    {{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }} ({{ $reserva->maquinaria->nro_inventario }})
                                </td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">
                                    {{-- Muestra el nombre del usuario, o 'N/A' si no está disponible --}}
                                    {{ $reserva->usuario->nombre ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800 font-bold">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">{{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">
                                    {{-- Estilos condicionales para el estado de la reserva --}}
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $reserva->estado === 'aprobada' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $reserva->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $reserva->estado === 'finalizada' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $reserva->estado === 'cancelada' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $reserva->estado }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 border-b text-sm text-gray-800">
                                    <div class="flex flex-col space-y-2">
                                        @php
                                            $today = \Carbon\Carbon::today();
                                            $fechaInicio = \Carbon\Carbon::parse($reserva->fecha_inicio);
                                            $fechaFin = \Carbon\Carbon::parse($reserva->fecha_fin);
                                            // La reserva debe estar aprobada y la fecha actual debe estar entre la fecha de inicio y la fecha de fin.
                                            $canDeliver = $reserva->estado === 'aprobada' && $today->gte($fechaInicio) && $today->lte($fechaFin);
                                        @endphp

                                        @if ($canDeliver)
                                            <form action="{{ route('reservas.registrar-entrega', $reserva->id_reserva) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="px-3 py-1 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition duration-200 text-xs w-full">
                                                    Marcar Entregada
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="px-3 py-1 bg-gray-400 text-gray-700 rounded-md cursor-not-allowed text-xs w-full" disabled>
                                                Entrega No Disponible
                                            </button>
                                        @endif
                                    </div>
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
@extends($layout)

@section('title', 'Panel de Entregas y Devoluciones')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Panel de Entregas y Devoluciones</h1>

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

    {{-- Selector de Pestañas/Secciones --}}
    <div class="mb-6 flex justify-center">
        <a href="{{ route('empleado.entregas-pendientes') }}"
           class="px-6 py-3 rounded-l-lg {{ request()->routeIs('empleado.entregas-pendientes') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} font-semibold transition duration-200">
            Reservas para Entregar
        </a>
        <a href="{{ route('empleado.devoluciones-pendientes') }}"
           class="px-6 py-3 rounded-r-lg {{ request()->routeIs('empleado.devoluciones-pendientes') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} font-semibold transition duration-200">
            Reservas para Devolver
        </a>
    </div>

    {{-- Contenido de Entregas Pendientes y Entregadas --}}
    @if(request()->routeIs('empleado.entregas-pendientes'))
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Registro y Gestión de Entregas</h2>
            @if(isset($reservasParaGestionEntrega) && $reservasParaGestionEntrega->isEmpty())
                <p class="text-gray-600 italic">No hay reservas para gestionar entregas en este momento.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 border-b border-gray-200">ID Reserva</th>
                                <th class="py-3 px-6 border-b border-gray-200">Cliente</th>
                                <th class="py-3 px-6 border-b border-gray-200">Maquinaria</th>
                                <th class="py-3 px-6 border-b border-gray-200">Fecha Inicio</th>
                                <th class="py-3 px-6 border-b border-gray-200">Fecha Fin</th>
                                <th class="py-3 px-6 border-b border-gray-200">Estado Actual</th>
                                <th class="py-3 px-6 border-b border-gray-200">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @foreach($reservasParaGestionEntrega as $reserva)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 whitespace-nowrap">{{ $reserva->id_reserva }}</td>
                                    <td class="py-3 px-6">{{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</td>
                                    <td class="py-3 px-6">{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">
                                        @if ($reserva->estado === 'aprobada')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Aprobada (Pendiente Entrega)
                                            </span>
                                        @elseif ($reserva->estado === 'en_curso')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Entregada (En Curso)
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($reserva->estado) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6">
                                        @if ($reserva->estado === 'aprobada')
                                            <form action="{{ route('reservas.registrar-entrega', $reserva->id_reserva) }}" method="POST" onsubmit="return confirm('¿Estás seguro de registrar la entrega de esta maquinaria?');" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn-primary-small bg-blue-500 hover:bg-blue-600">Registrar Entrega</button>
                                            </form>
                                        @else
                                            <span class="text-gray-500 italic">Acción Completada</span>
                                        @endif
                                        {{-- Botón de Cancelar Eliminado --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- Contenido de Devoluciones Pendientes y Finalizadas --}}
    @if(request()->routeIs('empleado.devoluciones-pendientes'))
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Registro y Gestión de Devoluciones</h2>
            @if(isset($reservasParaGestionDevolucion) && $reservasParaGestionDevolucion->isEmpty())
                <p class="text-gray-600 italic">No hay reservas para gestionar devoluciones en este momento.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-gray-100 text-left text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 border-b border-gray-200">ID Reserva</th>
                                <th class="py-3 px-6 border-b border-gray-200">Cliente</th>
                                <th class="py-3 px-6 border-b border-gray-200">Maquinaria</th>
                                <th class="py-3 px-6 border-b border-gray-200">Fecha Inicio</th>
                                <th class="py-3 px-6 border-b border-gray-200">Fecha Fin</th>
                                <th class="py-3 px-6 border-b border-gray-200">Estado Actual</th>
                                <th class="py-3 px-6 border-b border-gray-200">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @foreach($reservasParaGestionDevolucion as $reserva)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 whitespace-nowrap">{{ $reserva->id_reserva }}</td>
                                    <td class="py-3 px-6">{{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</td>
                                    <td class="py-3 px-6">{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">
                                        @if ($reserva->estado === 'en_curso')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                En Curso (Pendiente Devolución)
                                            </span>
                                        @elseif ($reserva->estado === 'finalizada')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Finalizada (Devuelta)
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($reserva->estado) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6">
                                        @if ($reserva->estado === 'en_curso')
                                            <form action="{{ route('reservas.registrar-devolucion', $reserva->id_reserva) }}" method="POST" onsubmit="return confirm('¿Estás seguro de registrar la devolución de esta maquinaria?');" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn-primary-small bg-orange-500 hover:bg-orange-600">Registrar Devolución</button>
                                            </form>
                                        @else
                                            <span class="text-gray-500 italic">Acción Completada</span>
                                        @endif
                                        {{-- Botón de Cancelar Eliminado --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
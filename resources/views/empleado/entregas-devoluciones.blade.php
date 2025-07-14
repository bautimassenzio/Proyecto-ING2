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

    {{-- Contenido para Entregas --}}
    @if(request()->routeIs('empleado.entregas-pendientes'))
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Entregas Pendientes (Hoy / Próximas)</h2>
            @if(isset($entregasPendientesHoyOProximas) && $entregasPendientesHoyOProximas->isEmpty())
                <p class="text-gray-600 italic mb-4">No hay reservas pendientes de entrega para hoy o próximas.</p>
            @else
                <div class="overflow-x-auto mb-6">
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
                            @foreach($entregasPendientesHoyOProximas as $reserva)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 whitespace-nowrap">{{ $reserva->id_reserva }}</td>
                                    <td class="py-3 px-6">{{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</td>
                                    <td class="py-3 px-6">{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Aprobada (Pendiente Entrega)
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        @php
                                            $fechaInicio = \Carbon\Carbon::parse($reserva->fecha_inicio);
                                        @endphp
                                        @if ($fechaInicio->isToday() || $fechaInicio->isPast())
                                            <form action="{{ route('reservas.registrar-entrega', $reserva->id_reserva) }}" method="POST" onsubmit="return confirm('¿Estás seguro de registrar la entrega de esta maquinaria?');" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn-primary-small bg-blue-500 hover:bg-blue-600">Registrar Entrega</button>
                                            </form>
                                        @else
                                            <span class="text-gray-500 italic">Entrega en futura fecha</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <h2 class="text-2xl font-semibold text-gray-700 mb-4 mt-8">Historial de Entregas</h2>
            @if(isset($entregasHistorial) && $entregasHistorial->isEmpty())
                <p class="text-gray-600 italic">No hay historial de entregas para mostrar.</p>
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
                            @foreach($entregasHistorial as $reserva)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 whitespace-nowrap">{{ $reserva->id_reserva }}</td>
                                    <td class="py-3 px-6">{{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</td>
                                    <td class="py-3 px-6">{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">
                                        @if ($reserva->estado === 'en_curso')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Entregada (En Curso)
                                            </span>
                                        @elseif ($reserva->estado === 'finalizada')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Finalizada
                                            </span>
                                        @elseif ($reserva->estado === 'aprobada')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Aprobada (Expirada)
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($reserva->estado) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="text-gray-500 italic">Acción Completada</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- Contenido para Devoluciones --}}
    @if(request()->routeIs('empleado.devoluciones-pendientes'))
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Devoluciones Pendientes</h2>
            @if(isset($devolucionesPendientes) && $devolucionesPendientes->isEmpty())
                <p class="text-gray-600 italic mb-4">No hay reservas pendientes de devolución en este momento.</p>
            @else
                <div class="overflow-x-auto mb-6">
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
                            @foreach($devolucionesPendientes as $reserva)
                                @php
                                    // Obtener la fecha actual sin la hora para la comparación
                                    $today = \Carbon\Carbon::today();
                                    // Parsear la fecha de fin de la reserva
                                    $fechaFin = \Carbon\Carbon::parse($reserva->fecha_fin);
                                    // Determinar si la fecha de fin es anterior a hoy
                                    $isOverdue = $fechaFin->lt($today); // lt = less than
                                @endphp
                                <tr class="border-b border-gray-200 hover:bg-gray-50 {{ $isOverdue ? 'bg-red-50' : '' }}"> {{-- Aplica clase si está atrasada --}}
                                    <td class="py-3 px-6 whitespace-nowrap">{{ $reserva->id_reserva }}</td>
                                    <td class="py-3 px-6">{{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</td>
                                    <td class="py-3 px-6">{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">
                                        <span class="{{ $isOverdue ? 'text-red-700 font-bold' : '' }}"> {{-- Texto en rojo si está atrasada --}}
                                            {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}
                                            @if ($isOverdue)
                                                <br><span class="text-red-500 text-xs">(¡Atrasada!)</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            En Curso (Pendiente Devolución)
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <form action="{{ route('reservas.registrar-devolucion', $reserva->id_reserva) }}" method="POST" onsubmit="return confirm('¿Estás seguro de registrar la devolución de esta maquinaria?');" class="inline-block">
                                            @csrf
                                            <button type="submit" class="btn-primary-small bg-orange-500 hover:bg-orange-600">Registrar Devolución</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <h2 class="text-2xl font-semibold text-gray-700 mb-4 mt-8">Historial de Devoluciones</h2>
            @if(isset($devolucionesHistorial) && $devolucionesHistorial->isEmpty())
                <p class="text-gray-600 italic">No hay historial de devoluciones para mostrar.</p>
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
                            @foreach($devolucionesHistorial as $reserva)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 whitespace-nowrap">{{ $reserva->id_reserva }}</td>
                                    <td class="py-3 px-6">{{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</td>
                                    <td class="py-3 px-6">{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">{{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Finalizada (Devuelta)
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="text-gray-500 italic">Acción Completada</span>
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

@section('additional-styles')
<style>
    /* Asegúrate de que tus variables CSS estén definidas aquí o en layouts.base si no lo están */
    :root {
        --primary-yellow: #FFC107;
        --secondary-yellow: #FFD54F;
        --dark-bg: #343a40;
        --text-dark: #212529;
        --text-light: #6c757d;

        /* Colores Tailwind aproximados para los botones de estado */
        --blue-500: #3b82f6;
        --blue-600: #2563eb;
        --green-500: #22c55e;
        --green-600: #16a34a;
        --yellow-500: #eab308;
        --yellow-600: #ca8a04;
        --red-500: #ef4444;
        --orange-500: #f97316; /* Para el botón de devolución */
        --orange-600: #ea580c; /* Para el hover del botón de devolución */
    }

    /* Clases de utilidad de Tailwind si no las tienes importadas directamente */
    .container { width: 100%; margin-right: auto; margin-left: auto; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .p-4 { padding: 1rem; }
    .text-3xl { font-size: 1.875rem; }
    .font-bold { font-weight: 700; }
    .text-gray-800 { color: #2d3748; }
    .mb-6 { margin-bottom: 1.5rem; }
    .bg-green-100 { background-color: #f0fff4; }
    .border-green-400 { border-color: #68d391; }
    .text-green-700 { color: #2f855a; }
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .rounded { border-radius: 0.25rem; }
    .relative { position: relative; }
    .mb-4 { margin-bottom: 1rem; }
    .block { display: block; }
    .sm\:inline { display: inline; } /* Considera si necesitas media queries para esto */
    .bg-red-100 { background-color: #fff5f5; }
    .border-red-400 { border-color: #fc8181; }
    .text-red-700 { color: #c53030; }
    .mb-6 { margin-bottom: 1.5rem; }
    .flex { display: flex; }
    .justify-center { justify-content: center; }
    .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .rounded-l-lg { border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; }
    .rounded-r-lg { border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
    .bg-blue-600 { background-color: var(--blue-600); }
    .text-white { color: #fff; }
    .bg-gray-200 { background-color: #edf2f7; }
    .text-gray-700 { color: #4a5568; }
    .hover\:bg-gray-300:hover { background-color: #cbd5e0; }
    .font-semibold { font-weight: 600; }
    .transition { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
    .bg-white { background-color: #fff; }
    .rounded-lg { border-radius: 0.5rem; }
    .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .p-6 { padding: 1.5rem; }
    .text-2xl { font-size: 1.5rem; }
    .text-gray-700 { color: #4a5568; }
    .mb-4 { margin-bottom: 1rem; }
    .text-gray-600 { color: #718096; }
    .italic { font-style: italic; }
    .overflow-x-auto { overflow-x: auto; }
    .min-w-full { min-width: 100%; }
    .border { border-width: 1px; }
    .border-gray-200 { border-color: #edf2f7; }
    .bg-gray-100 { background-color: #f7fafc; }
    .text-left { text-align: left; }
    .uppercase { text-transform: uppercase; }
    .text-sm { font-size: 0.875rem; }
    .leading-normal { line-height: 1.5; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
    .border-b { border-bottom-width: 1px; }
    .whitespace-nowrap { white-space: nowrap; }
    .hover\:bg-gray-50:hover { background-color: #f9fafb; }
    .inline-flex { display: inline-flex; }
    .text-xs { font-size: 0.75rem; }
    .leading-5 { line-height: 1.25rem; }
    .rounded-full { border-radius: 9999px; }
    .bg-blue-100 { background-color: #ebf8ff; }
    .text-blue-800 { color: #2c5282; }
    .bg-red-100 { background-color: #fff5f5; }
    .text-red-800 { color: #9b2c2c; }
    .bg-yellow-100 { background-color: #fffff0; }
    .text-yellow-800 { color: #8b542f; }
    .bg-green-100 { background-color: #f0fff4; }
    .text-green-800 { color: #2f855a; }
    .bg-gray-100 { background-color: #f7fafc; }
    .text-gray-800 { color: #2d3748; }
    .inline-block { display: inline-block; }
    .bg-blue-500 { background-color: var(--blue-500); }
    .hover\:bg-blue-600:hover { background-color: var(--blue-600); }
    .text-gray-500 { color: #a0aec0; }
    .bg-orange-100 { background-color: #fffaf0; }
    .text-orange-800 { color: #9c4221; }
    .bg-orange-500 { background-color: var(--orange-500); }
    .hover\:bg-orange-600:hover { background-color: var(--orange-600); }
    .mt-8 { margin-top: 2rem; }
    .mb-8 { margin-bottom: 2rem; } /* Added for spacing between sections */

    /* Estilos para botones de acción (Mantener consistencia) */
    .btn-primary-small {
        background-color: var(--primary-yellow); /* Puedes ajustar este color si quieres que sea diferente de los botones de estado */
        color: white; /* O var(--dark-bg) si quieres texto oscuro */
        font-weight: bold;
        padding: 0.5rem 1rem; /* Un poco más pequeño que el default de Tailwind para botones */
        border-radius: 0.375rem; /* rounded-md */
        border: 1px solid transparent; /* Para que el color del borde cambie al hover */
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        text-decoration: none;
        display: inline-block; /* Asegura que se comporte como un bloque de texto en línea */
        line-height: 1.25; /* Para centrar el texto verticalmente */
    }

    .btn-primary-small:hover {
        opacity: 0.9; /* Ligeramente más opaco al pasar el ratón */
        transform: translateY(-1px); /* Efecto sutil de levantamiento */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Nuevo estilo para filas atrasadas */
    .bg-red-50 {
        background-color: #fef2f2; /* Un rojo muy claro para el fondo de la fila */
    }
</style>
@endsection
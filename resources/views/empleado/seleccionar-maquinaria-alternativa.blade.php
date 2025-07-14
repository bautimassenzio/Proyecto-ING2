@extends($layout)

@section('title', 'Seleccionar Maquinaria Alternativa')

@section('content')
<div class="container mx-auto p-4">
    {{-- IDENTIFICADOR ÚNICO DE VERSIÓN: V20240710-SOLUCION-DIRECTA --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Maquinaria solicitada no disponible</h1>

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
        <p class="text-700 mb-2"><strong>Maquinaria Original:</strong> {{ $maquinariaOriginal->marca }} {{ $maquinariaOriginal->modelo }} (Estado: <span class="font-bold text-red-600">{{ $maquinariaOriginal->estado }}</span>)</p>
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
                   class="btn-action btn-danger mr-2"> {{-- Usando la nueva clase btn-danger --}}
                    Cancelar Reserva
                </a>
                <a href="{{ route('empleado.panel-entregas-devoluciones') }}" class="btn-action btn-secondary">Volver al Panel</a> {{-- Usando la nueva clase btn-secondary --}}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Maquinarias Alternativas Disponibles en {{ $maquinariaOriginal->localidad->nombre ?? 'esta localidad' }}</h2>
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
                                        <button type="submit" class="btn-action btn-primary">Seleccionar y Entregar</button> {{-- Usando la nueva clase btn-primary --}}
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
                   class="btn-action btn-danger mr-2"> {{-- Usando la nueva clase btn-danger --}}
                    Cancelar Reserva
                </a>
                <a href="{{ route('empleado.panel-entregas-devoluciones') }}" class="btn-action btn-secondary">Volver al Panel</a> {{-- Usando la nueva clase btn-secondary --}}
            </div>
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

        /* Colores Tailwind aproximados para los botones y estados */
        --blue-500: #3b82f6;
        --blue-600: #2563eb;
        --green-500: #22c55e;
        --green-600: #16a34a;
        --yellow-500: #eab308;
        --yellow-600: #ca8a04;
        --red-500: #ef4444;
        --red-600: #dc2626;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
    }

    /* Clases de utilidad de Tailwind si no las tienes importadas directamente */
    .container { width: 100%; margin-right: auto; margin-left: auto; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .p-4 { padding: 1rem; }
    .text-3xl { font-size: 1.875rem; }
    .font-bold { font-weight: 700; }
    .text-gray-800 { color: #2d3748; }
    .mb-6 { margin-bottom: 1.5rem; }
    .bg-green-100 { background-color: #d1fae5; } /* lighter green */
    .border-green-400 { border-color: #34d399; } /* medium green */
    .text-green-700 { color: #047857; } /* darker green */
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .rounded { border-radius: 0.25rem; }
    .relative { position: relative; }
    .mb-4 { margin-bottom: 1rem; }
    .block { display: block; }
    .sm\:inline { display: inline; }
    .bg-red-100 { background-color: #fee2e2; } /* lighter red */
    .border-red-400 { border-color: #f87171; } /* medium red */
    .text-red-700 { color: #b91c1c; } /* darker red */
    .mb-6 { margin-bottom: 1.5rem; }
    .flex { display: flex; }
    .justify-end { justify-content: flex-end; } /* Changed from justify-center for right alignment of buttons */
    .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .rounded-l-lg { border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; }
    .rounded-r-lg { border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
    .bg-blue-600 { background-color: var(--blue-600); }
    .text-white { color: #fff; }
    .bg-gray-200 { background-color: #e5e7eb; } /* lighter gray */
    .text-gray-700 { color: #374151; } /* darker gray */
    .hover\:bg-gray-300:hover { background-color: #d1d5db; } /* hover for light gray */
    .font-semibold { font-weight: 600; }
    .transition { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
    .bg-white { background-color: #fff; }
    .rounded-lg { border-radius: 0.5rem; }
    .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .p-6 { padding: 1.5rem; }
    .text-2xl { font-size: 1.5rem; }
    .mb-4 { margin-bottom: 1rem; }
    .text-gray-600 { color: #4b5563; } /* medium gray */
    .italic { font-style: italic; }
    .overflow-x-auto { overflow-x: auto; }
    .min-w-full { min-width: 100%; }
    .border { border-width: 1px; }
    .border-gray-200 { border-color: #e5e7eb; }
    .bg-gray-100 { background-color: #f3f4f6; } /* even lighter gray */
    .text-left { text-align: left; }
    .uppercase { text-transform: uppercase; }
    .text-sm { font-size: 0.875rem; }
    .leading-normal { line-height: 1.5; }
    .border-b { border-bottom-width: 1px; }
    .whitespace-nowrap { white-space: nowrap; }
    .hover\:bg-gray-50:hover { background-color: #f9fafb; }
    .mr-2 { margin-right: 0.5rem; } /* Added for button spacing */
    .mt-6 { margin-top: 1.5rem; } /* Added for spacing */

    /* Base button style (replaces btn-primary-small with more complete styles) */
    .btn-action {
        display: inline-flex; /* Use inline-flex for better vertical alignment of text/icons */
        align-items: center;
        justify-content: center;
        font-weight: bold;
        padding: 0.6rem 1.2rem; /* Slightly more padding for a better feel */
        border-radius: 0.375rem; /* rounded-md */
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        text-decoration: none; /* Remove underline for links */
        line-height: 1.25; /* Ensure text is vertically centered */
        white-space: nowrap; /* Prevent text wrapping inside button */
    }

    /* Primary button style */
    .btn-primary {
        background-color: var(--blue-500);
        color: white;
    }
    .btn-primary:hover {
        background-color: var(--blue-600);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Danger button style (for Cancel) */
    .btn-danger {
        background-color: var(--red-500);
        color: white;
    }
    .btn-danger:hover {
        background-color: var(--red-600);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Secondary/Neutral button style (for Volver al Panel) */
    .btn-secondary {
        background-color: var(--gray-500);
        color: white;
    }
    .btn-secondary:hover {
        background-color: var(--gray-600);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
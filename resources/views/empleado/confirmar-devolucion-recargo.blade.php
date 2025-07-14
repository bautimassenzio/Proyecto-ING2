@extends($layout)

@section('title', 'Confirmar Devolución con Recargo')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Confirmar Devolución con Recargo</h1>

    {{-- Mensaje de advertencia de recargo --}}
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">¡ATENCIÓN!</strong>
        <span class="block sm:inline">Esta reserva tiene un recargo por demora en la devolución.</span>
    </div>

    {{-- Sección de Detalles de la Reserva --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-blue-500">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Detalles de la Reserva</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600 mb-2"><strong>ID Reserva:</strong> <span class="font-medium">{{ $reserva->id_reserva }}</span></p>
                <p class="text-gray-600 mb-2"><strong>Cliente:</strong> <span class="font-medium">{{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</span></p>
                <p class="text-gray-600 mb-2"><strong>Maquinaria:</strong> <span class="font-medium">{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</span></p>
            </div>
            <div>
                <p class="text-gray-600 mb-2"><strong>Fecha de Inicio:</strong> <span class="font-medium">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</span></p>
                <p class="text-gray-600 mb-2"><strong>Fecha de Fin Original:</strong> <span class="font-medium text-blue-700">{{ $fechaFinOriginal }}</span></p>
                <p class="text-gray-600 mb-2"><strong>Fecha de Devolución Real:</strong> <span class="font-medium text-red-700">{{ $fechaDevolucionReal }}</span></p>
            </div>
        </div>
    </div>

    {{-- Sección de Detalle del Recargo --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-red-500">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Detalle del Recargo</h2>
        <p class="text-lg text-gray-800 mb-2">
            La maquinaria fue devuelta con <strong class="text-red-700">{{ $diasDemora }}</strong> día(s) de demora.
        </p>
        <p class="text-lg text-gray-800 mb-4">
            El monto total del recargo a cobrar es de:
            <strong class="text-green-600 text-4xl block mt-2">${{ number_format($recargo, 2, ',', '.') }}</strong> {{-- Increased font size for emphasis --}}
        </p>
        <p class="text-gray-600 italic border-t border-gray-200 pt-4 mt-4">
            <i class="fas fa-info-circle mr-2"></i> Por favor, informe este monto al cliente y cobre el recargo en efectivo.
        </p>
    </div>

    {{-- Controles de Acción (Botones) --}}
    <div class="flex justify-end gap-4">
        <a href="{{ route('empleado.devoluciones-pendientes') }}" class="btn-action btn-secondary"> {{-- Usamos btn-action y btn-secondary --}}
            Volver al Listado
        </a>
        <form action="{{ route('reservas.finalizar-devolucion', $reserva->id_reserva) }}" method="POST" onsubmit="return confirm('¿Ha cobrado el recargo y está seguro de finalizar la devolución?');">
            @csrf
            @method('PUT')
            <button type="submit" class="btn-action btn-success"> {{-- Usamos btn-action y btn-success (verde) --}}
                Confirmar y Finalizar Devolución
            </button>
        </form>
    </div>
</div>
@endsection

@section('additional-styles')
<style>
    /* Variables de color de Tailwind (si no están ya en un layout base) */
    :root {
        --blue-500: #3b82f6;
        --blue-600: #2563eb;
        --green-500: #22c55e; /* Un verde vibrante para éxito/confirmación */
        --green-600: #16a34a;
        --red-500: #ef4444;
        --red-600: #dc2626;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --yellow-500: #eab308;
    }

    /* Clases base de Tailwind CSS (replicadas si no usas el framework completo) */
    .container { max-width: 1200px; margin-right: auto; margin-left: auto; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .p-4 { padding: 1rem; }
    .text-3xl { font-size: 1.875rem; } /* 30px */
    .text-4xl { font-size: 2.25rem; } /* 36px, for emphasis on the amount */
    .font-bold { font-weight: 700; }
    .text-gray-800 { color: #1f2937; }
    .mb-6 { margin-bottom: 1.5rem; }
    .bg-red-100 { background-color: #fee2e2; }
    .border-red-400 { border-color: #f87171; }
    .text-red-700 { color: #b91c1c; }
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .rounded { border-radius: 0.25rem; }
    .relative { position: relative; }
    .mb-6 { margin-bottom: 1.5rem; }
    .block { display: block; }
    .sm\:inline { display: inline; }
    .bg-white { background-color: #fff; }
    .rounded-lg { border-radius: 0.5rem; }
    .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .p-6 { padding: 1.5rem; }
    .text-2xl { font-size: 1.5rem; } /* 24px */
    .font-semibold { font-weight: 600; }
    .mb-4 { margin-bottom: 1rem; }
    .grid { display: grid; }
    .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .md\:grid-cols-2 { /* md breakpoint */ grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .gap-4 { gap: 1rem; }
    .text-gray-600 { color: #4b5563; }
    .mb-2 { margin-bottom: 0.5rem; }
    .text-blue-700 { color: #1d4ed8; }
    .text-lg { font-size: 1.125rem; } /* 18px */
    .text-green-600 { color: #059669; }
    .mt-2 { margin-top: 0.5rem; }
    .italic { font-style: italic; }
    .border-l-4 { border-left-width: 4px; }
    .border-blue-500 { border-color: var(--blue-500); }
    .border-red-500 { border-color: var(--red-500); }
    .border-t { border-top-width: 1px; }
    .border-gray-200 { border-color: #e5e7eb; }
    .pt-4 { padding-top: 1rem; }
    .mt-4 { margin-top: 1rem; }
    .flex { display: flex; }
    .justify-end { justify-content: flex-end; }
    .gap-4 { gap: 1rem; } /* Spacing between buttons */
    .mr-2 { margin-right: 0.5rem; } /* Standard Tailwind spacing */
    .font-medium { font-weight: 500; }

    /* Estilos base para todos los botones de acción */
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        padding: 0.6rem 1.2rem;
        border-radius: 0.375rem; /* rounded-md */
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        text-decoration: none; /* Elimina el subrayado para los <a> */
        line-height: 1.25; /* Centrado vertical del texto */
        white-space: nowrap; /* Evita que el texto se rompa */
    }

    /* Estilo para el botón de Confirmar (verde) */
    .btn-success {
        background-color: var(--green-500);
        color: white;
    }
    .btn-success:hover {
        background-color: var(--green-600);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Estilo para el botón Volver al Listado (gris) */
    .btn-secondary {
        background-color: var(--gray-500);
        color: white;
    }
    .btn-secondary:hover {
        background-color: var(--gray-600);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Icono de FontAwesome para el mensaje del recargo */
    @import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");
</style>
@endsection
@extends($layout)

@section('title', 'Confirmar Devolución con Recargo')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Confirmar Devolución con Recargo</h1>

    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">¡ATENCIÓN!</strong>
        <span class="block sm:inline">Esta reserva tiene un recargo por demora en la devolución.</span>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Detalles de la Reserva</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600"><strong>ID Reserva:</strong> {{ $reserva->id_reserva }}</p>
                <p class="text-gray-600"><strong>Cliente:</strong> {{ $reserva->cliente->nombre }} (DNI: {{ $reserva->cliente->dni }})</p>
                <p class="text-gray-600"><strong>Maquinaria:</strong> {{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}</p>
            </div>
            <div>
                <p class="text-gray-600"><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</p>
                <p class="text-gray-600"><strong>Fecha de Fin Original:</strong> {{ $fechaFinOriginal }}</p>
                <p class="text-gray-600"><strong>Fecha de Devolución Real:</strong> {{ $fechaDevolucionReal }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Detalle del Recargo</h2>
        <p class="text-lg text-gray-800 mb-2">
            La maquinaria fue devuelta con <strong class="text-red-700">{{ $diasDemora }}</strong> día(s) de demora.
        </p>
        <p class="text-lg text-gray-800 mb-4">
            El monto total del recargo a cobrar es de:
            <strong class="text-green-600 text-3xl block mt-2">${{ number_format($recargo, 2, ',', '.') }}</strong>
        </p>
        <p class="text-gray-600 italic">
            Por favor, informe este monto al cliente y cobre el recargo en efectivo.
        </p>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('empleado.devoluciones-pendientes') }}" class="btn-cancel bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
            Volver al Listado
        </a>
        <form action="{{ route('reservas.finalizar-devolucion', $reserva->id_reserva) }}" method="POST" onsubmit="return confirm('¿Ha cobrado el recargo y está seguro de finalizar la devolución?');">
            @csrf
            @method('PUT')
            <button type="submit" class="btn-primary bg-green-500 hover:bg-green-600">
                Confirmar y Finalizar Devolución
            </button>
        </form>
    </div>
</div>
@endsection
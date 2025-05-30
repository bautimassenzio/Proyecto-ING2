<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Crear Nueva Reserva</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reservas.store') }}" method="POST">
            @csrf

            {{-- Aquí puedes mostrar el nombre del cliente autenticado si lo deseas, pero no el selector --}}
            @if (isset($clienteAutenticado))
                <div class="mb-4 p-3 bg-blue-100 border border-blue-400 text-blue-700 rounded-md">
                    <p class="text-sm font-semibold">Realizando reserva para:</p>
                    <p class="text-lg font-bold">{{ $clienteAutenticado->nombre }} ({{ $clienteAutenticado->email }})</p>
                    {{-- No se necesita un campo oculto para id_cliente si lo obtienes del Auth::id() en el controlador --}}
                </div>
            @endif

            <div class="mb-4">
                <label for="fecha_inicio" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Inicio:</label>
                <input type="text" name="fecha_inicio" id="fecha_inicio" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fecha_inicio') border-red-500 @enderror" placeholder="Selecciona una fecha" value="{{ old('fecha_inicio') }}">
                @error('fecha_inicio')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="fecha_fin" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Fin:</label>
                <input type="text" name="fecha_fin" id="fecha_fin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fecha_fin') border-red-500 @enderror" placeholder="Selecciona una fecha" value="{{ old('fecha_fin') }}">
                @error('fecha_fin')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Crear Reserva
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#fecha_inicio", {
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const startDate = selectedDates[0];
                    flatpickr("#fecha_fin", {
                        dateFormat: "Y-m-d",
                        minDate: new Date(startDate.setDate(startDate.getDate() + 1)) // Fecha fin debe ser al menos un día después de inicio
                    });
                }
            }
        });

        flatpickr("#fecha_fin", {
            dateFormat: "Y-m-d",
            minDate: "today" // Por si se selecciona primero la fecha de fin
        });
    </script>
</body>
</html>
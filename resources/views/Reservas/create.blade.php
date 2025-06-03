<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Crear Nueva Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <style>
        /* Estilo para sombrear las fechas deshabilitadas */
        .flatpickr-day.flatpickr-disabled {
            background-color: #f8d7da !important; /* Un rojo claro, similar a danger en Bootstrap */
            color: #721c24 !important; /* Texto oscuro para contraste */
            opacity: 0.6;
            cursor: not-allowed;
        }
        /* Opcional: si quieres un estilo para fechas pasadas diferentes */
        .flatpickr-day.flatpickr-disabled.flatpickr-day.prevMonthDay,
        .flatpickr-day.flatpickr-disabled.flatpickr-day.nextMonthDay {
            opacity: 0.3;
        }
    </style>
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

            <input type="hidden" name="id_maquinaria" value="{{ $maquinaria->id_maquinaria }}">

            @if (isset($clienteAutenticado))
                <div class="mb-4 p-3 bg-blue-100 border border-blue-400 text-blue-700 rounded-md">
                    <p class="text-sm font-semibold">Realizando reserva para:</p>
                    <p class="text-lg font-bold">{{ $clienteAutenticado->nombre }} ({{ $clienteAutenticado->email }})</p>
                </div>
            @endif

            <div class="mb-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-md">
                <p class="text-sm font-semibold">Reservando maquinaria:</p>
                <p class="text-lg font-bold">{{ $maquinaria->marca }} {{ $maquinaria->modelo }} ({{ $maquinaria->numero_inventario }})</p>
            </div>

            <div class="mb-4">
                <label for="fecha_inicio" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Inicio:</label>
                <input type="text" name="fecha_inicio" id="fecha_inicio"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fecha_inicio') border-red-500 @enderror"
                    placeholder="Selecciona una fecha" value="{{ old('fecha_inicio') }}">
                @error('fecha_inicio')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="fecha_fin" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Fin:</label>
                <input type="text" name="fecha_fin" id="fecha_fin"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fecha_fin') border-red-500 @enderror"
                    placeholder="Selecciona una fecha" value="{{ old('fecha_fin') }}">
                @error('fecha_fin')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Crear Reserva
                </button>

                <a href="{{ route('catalogo.index') }}"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Volver al catálogo
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        window.fechasOcupadas = @json($fechasOcupadas); // Esto ya te trae los objetos {fecha_inicio, fecha_fin}

        // Función para generar una lista de fechas individuales a partir de rangos
        function getDatesInRange(ranges) {
            let dates = [];
            ranges.forEach(range => {
                let startDate = new Date(range.fecha_inicio);
                let endDate = new Date(range.fecha_fin);
                // Si la reserva es de un solo día (inicio y fin iguales), asegúrate de incluir ese día
                if (startDate.toDateString() === endDate.toDateString()) {
                    dates.push(startDate.toISOString().split('T')[0]); // Solo la fecha YYYY-MM-DD
                    return;
                }
                for (let d = startDate; d <= endDate; d.setDate(d.getDate() + 1)) {
                    dates.push(new Date(d).toISOString().split('T')[0]); // Solo la fecha YYYY-MM-DD
                }
            });
            return dates;
        }

        const diasBloqueados = getDatesInRange(window.fechasOcupadas);

        // Inicializar Flatpickr para Fecha de Inicio
        const inicioPicker = flatpickr("#fecha_inicio", {
            dateFormat: "Y-m-d",
            minDate: "today",
            // Deshabilitar días individuales
            disable: diasBloqueados,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const startDate = new Date(selectedDates[0]);
                    // Asegúrate de que la fecha de fin no pueda ser anterior a la fecha de inicio
                    // Suma un día para que el mínimo de fin sea el día siguiente al inicio
                    const minDateFin = new Date(startDate);
                    minDateFin.setDate(startDate.getDate() + 1);
                    finPicker.set('minDate', minDateFin);

                    // También deshabilita los rangos ocupados en el segundo picker
                    // Aquí podrías necesitar una lógica más compleja si quieres que la selección de un rango
                    // no pueda "saltar" un día deshabilitado.
                    finPicker.set('disable', [
                        ...diasBloqueados,
                        function(date) {
                            // Si ya seleccionaste una fecha de inicio, no permitas seleccionar una fecha de fin
                            // que sea antes o igual a la fecha de inicio seleccionada.
                            return date <= startDate;
                        }
                    ]);
                }
            }
        });

        // Inicializar Flatpickr para Fecha de Fin
        const finPicker = flatpickr("#fecha_fin", {
            dateFormat: "Y-m-d",
            minDate: "today", // Por defecto, mínimo hoy
            // Deshabilitar días individuales (los mismos días que en el picker de inicio)
            disable: diasBloqueados
        });
    </script>
</body>
</html>
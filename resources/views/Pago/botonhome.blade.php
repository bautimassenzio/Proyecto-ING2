{{-- resources/views/pago/estado.blade.php --}}

@extends($layout) {{-- Asegúrate de que 'layouts.base' sea el nombre correcto de tu layout --}}

@section('title', 'Estado de Pago') {{-- Título específico para esta página --}}

{{-- Opcional: Define la navegación para esta vista si la necesitas --}}


@section('content')
<div class="row justify-content-center text-center"> {{-- Centra el contenido en el medio de la página --}}
    <div class="col-md-8 col-lg-6"> {{-- Limita el ancho del contenido para mejor lectura --}}
        {{-- Muestra el mensaje de estado (éxito/error) --}}
        @if ($mensaje)
            <h1 class="mb-4">{{ $mensaje }}</h1> {{-- Clase de Bootstrap para margen inferior --}}
        @else
            <h1 class="mb-4">Estado de Pago Desconocido</h1>
        @endif

        {{-- Formulario para volver al inicio --}}
        {{-- CAMBIO AQUÍ: URL absoluta forzada a localhost --}}
        <form action="http://127.0.0.1:8000" method="get">
            <button type="submit" class="btn btn-primary btn-lg">Volver al inicio</button> {{-- Clases de Bootstrap para el botón --}}
        </form>
    </div>
</div>
@endsection

{{-- No se requieren scripts adicionales en este caso simple --}}
{{-- @section('additional-scripts')
<script>
    // Tu JavaScript aquí
</script>
@endsection --}}
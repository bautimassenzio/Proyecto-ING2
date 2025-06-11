{{-- resources/views/pago/seleccionar.blade.php (o la ruta a tu archivo) --}}

@extends($layout) {{-- Asegúrate de que 'layouts.base' sea el nombre correcto de tu layout --}}

@section('title', 'Seleccionar Método de Pago') {{-- Título específico para esta página --}}

{{-- Si necesitas navegación específica para usuarios logueados que están en el proceso de pago, puedes definirla aquí --}}


@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h1 class="text-center mb-4">Seleccione Método de Pago</h1>

        <div class="d-grid gap-3"> {{-- Usamos d-grid y gap-3 de Bootstrap para espaciar botones --}}
            <form action="{{ route('pago.procesar') }}" method="POST">
                @csrf
                {{-- Aplicamos las clases de botón de tu layout y Bootstrap --}}
                <button type="submit" class="btn btn-primary w-100 py-3">Pagar con Mercado Pago</button>
            </form>

            <div class="d-grid gap-3"> {{-- Usamos d-grid y gap-3 de Bootstrap para espaciar botones --}}
            <form action="{{ route('pago.procesar.tarjeta') }}" method="GET">
                @csrf
                {{-- Aplicamos las clases de botón de tu layout y Bootstrap --}}
                <button type="submit" class="btn btn-primary w-100 py-3">Pagar con Tarjeta</button>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Si necesitas scripts JS adicionales solo para esta vista, los pondrías aquí --}}
@section('additional-scripts')
<script>
    // Scripts JS específicos para la selección de pago
</script>
@endsection
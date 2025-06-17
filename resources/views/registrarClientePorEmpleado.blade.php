@extends($layout)

@section('content')


    <div class="container mt-5">
        <h2>Registro de Usuario</h2>


        <form method="POST" action="{{ route('registerByEmployee') }}">
            @csrf

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>


            <div class="mb-3">
                <label for="dni" class="form-label">DNI</label>
                <input type="text" class="form-control" id="dni" name="dni" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
            </div>

            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                <input 
                    type="date" 
                    class="form-control"
                    id="fecha_nacimiento" 
                    name="fecha_nacimiento" 
                    value="{{ old('fecha_nacimiento') }}"
                    max="{{ date('Y-m-d') }}" 
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
    </div>
    @endsection

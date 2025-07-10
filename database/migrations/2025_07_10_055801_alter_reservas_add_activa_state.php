    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\DB; // Necesario para DB::statement

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            // Primero, si tienes alguna reserva en estado 'pendiente' que no se actualizó antes,
            // asegúrate de que se convierta a 'aprobada' antes de modificar el ENUM.
            // Esto es crucial para evitar errores si 'pendiente' se elimina del ENUM.
            DB::table('reservas')
                ->where('estado', 'pendiente')
                ->update(['estado' => 'aprobada']);

            // Modifica la columna 'estado' para que sea un ENUM con los nuevos valores permitidos.
            // Los valores permitidos son: 'aprobada', 'activa', 'cancelada', 'finalizada'.
            // Asegúrate de que estos son los únicos estados que quieres permitir.
            Schema::table('reservas', function (Blueprint $table) {
                $table->enum('estado', ['aprobada', 'activa', 'cancelada', 'finalizada'])->change();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            // Para revertir, podrías cambiar el ENUM de nuevo a su estado anterior si lo conoces.
            // O, si es un cambio permanente, podrías simplemente no definir un down para esta parte.
            // Por ejemplo, si el estado original era solo 'pendiente', 'aprobada', 'cancelada', 'finalizada':
            Schema::table('reservas', function (Blueprint $table) {
                // NOTA: Revertir un ENUM puede ser complejo si los datos no coinciden.
                // Si la columna tenía 'pendiente' y quieres volver a ese estado,
                // primero deberías cambiar los 'activa' a 'aprobada' o 'pendiente' si aplica.
                // Por simplicidad, aquí se revierte a un estado común, pero ajusta si necesitas
                // un comportamiento de reversión más específico para tus datos.
                $table->enum('estado', ['aprobada', 'cancelada', 'finalizada', 'pendiente'])->change();
            });
        }
    };
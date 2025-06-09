<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Añade la columna 'role' como tipo string.
            // Le damos un valor por defecto 'cliente' para los usuarios existentes y nuevos si no se especifica.
            // La colocamos después de 'fecha_nacimiento' para mantener un orden lógico.
            $table->string('role')->default('cliente')->after('fecha_nacimiento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si haces un rollback de esta migración, elimina la columna 'role'.
            $table->dropColumn('role');
        });
    }
};
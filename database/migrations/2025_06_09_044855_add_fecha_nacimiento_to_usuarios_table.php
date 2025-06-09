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
        Schema::table('usuarios', function (Blueprint $table) {
            // Añade la columna 'fecha_nacimiento' como tipo DATE y nullable.
            // La colocamos después de 'estado' para que coincida con tu estructura existente.
            if (!Schema::hasColumn('usuarios', 'fecha_nacimiento')) {
                $table->date('fecha_nacimiento')->nullable()->after('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Si haces un rollback de esta migración, elimina la columna.
            if (Schema::hasColumn('usuarios', 'fecha_nacimiento')) {
                $table->dropColumn('fecha_nacimiento');
            }
        });
    }
};
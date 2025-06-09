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
            // Añade la columna 'role' como string, con un valor por defecto 'cliente'
            // (o 'user' si ese es tu rol predeterminado para nuevos registros)
            // y colócala después de 'password'.
            $table->string('role')->default('cliente')->after('password');
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
            // Cuando hagas rollback, elimina la columna 'role'
            $table->dropColumn('role');
        });
    }
};

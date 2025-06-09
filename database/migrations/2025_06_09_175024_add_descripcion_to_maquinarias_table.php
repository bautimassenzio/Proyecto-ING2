<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maquinarias', function (Blueprint $table) {
            // Agrega la columna 'descripcion' después de 'estado'
            // Es de tipo TEXT porque puede ser una descripción larga y es NULLABLE (opcional)
            $table->text('descripcion')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maquinarias', function (Blueprint $table) {
            // Elimina la columna 'descripcion' si se revierte la migración
            $table->dropColumn('descripcion');
        });
    }
};
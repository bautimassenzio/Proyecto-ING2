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
        Schema::table('reservas', function (Blueprint $table) {
            // Esta línea agrega created_at y updated_at.
            // Si tienes datos existentes, considera .nullable() o valores por defecto.
            // La forma más simple si no te preocupan los valores por defecto iniciales es:
            $table->timestamps();
            // O si ya tienes datos y necesitas que sean null inicialmente:
            // $table->timestamp('created_at')->nullable();
            // $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
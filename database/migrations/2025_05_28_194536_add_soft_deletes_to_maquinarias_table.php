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
    public function up()
    {
        Schema::table('maquinarias', function (Blueprint $table) {
            // Añade la columna 'deleted_at' que es de tipo timestamp y nullable
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maquinarias', function (Blueprint $table) {
            // Para revertir la migración, elimina la columna 'deleted_at'
            $table->dropSoftDeletes();
        });
    }
};

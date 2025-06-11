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
        $table->text('descripcion')->nullable(); // o ->default('') si lo prefieres
    });
}

public function down()
{
    Schema::table('maquinarias', function (Blueprint $table) {
        $table->dropColumn('descripcion');
    });
}

};

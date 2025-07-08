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
    Schema::create('tipos_de_uso', function (Blueprint $table) {
        $table->id();
        $table->string('nombre'); // o el campo que uses para describir el tipo de uso
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('tipos_de_uso');
}

};

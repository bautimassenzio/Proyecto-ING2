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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('id_usuario', true);
            $table->string('nombre', 100);
            $table->string('email', 100)->unique('email');
            $table->string('contraseña');
            $table->enum('rol', ['cliente', 'empleado', 'admin']);
            $table->string('dni', 20)->unique('dni');
            $table->string('telefono', 20)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->nullable()->default('activo');
            $table->date('fecha_nacimiento')->nullable();
            $table->date('fecha_alta')->nullable()->default('CURRENT_DATE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};

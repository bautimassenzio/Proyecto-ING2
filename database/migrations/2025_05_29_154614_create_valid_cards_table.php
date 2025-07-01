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
        Schema::create('valid_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number')->unique();
            $table->string('expiry_month', 2);
            $table->string('expiry_year', 2);
            $table->string('cvv', 4);
            $table->string('cardholder_name')->nullable();
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('valid_cards');
    }
};

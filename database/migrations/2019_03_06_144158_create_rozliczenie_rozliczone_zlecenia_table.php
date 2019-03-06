<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRozliczenieRozliczoneZleceniaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rozliczenie_rozliczone_zlecenia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pracownik');
            $table->unsignedInteger('rozliczenie_id');
            $table->unsignedInteger('zlecenie_id');
            $table->string('producent_type');
            $table->string('robocizny', 191)->default('[]');
            $table->string('dojazdy', 191)->default('[]');
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
        Schema::dropIfExists('rozliczenie_rozliczone_zlecenia');
    }
}

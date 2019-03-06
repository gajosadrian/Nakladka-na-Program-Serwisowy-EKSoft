<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRozliczeniaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rozliczenia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pracownik');
            $table->unsignedSmallInteger('rok');
            $table->unsignedTinyInteger('miesiac');
            $table->unsignedInteger('suma_robocizn')->default(0);
            $table->unsignedInteger('suma_dojazdow')->default(0);
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
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
        Schema::dropIfExists('rozliczenia');
    }
}

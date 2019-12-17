<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZlecenieZdjeciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zlecenie_zdjecia', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zlecenie_id')->nullable();
            $table->unsignedInteger('urzadzenie_id')->nullable();
            $table->string('type')->nullable();
            $table->string('path');
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
        Schema::dropIfExists('zlecenie_zdjecia');
    }
}

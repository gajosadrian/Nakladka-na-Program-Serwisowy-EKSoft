<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNaszykowaneCzesciTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('naszykowane_czesci', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('user_id_sprawdzil')->nullable();
            $table->timestamp('sprawdzone_at')->nullable();
            $table->unsignedInteger('technik_id');
            $table->unsignedInteger('zlecenie_id');
            $table->timestamp('zlecenie_data')->nullable();
            $table->unsignedInteger('towar_id');
            $table->unsignedInteger('ilosc');
            $table->unsignedInteger('ilosc_do_zwrotu');
            $table->unsignedInteger('ilosc_zamontowane');
            $table->string('uwagi')->nullable();
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
        Schema::dropIfExists('naszykowane_czesci');
    }
}

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
            $table->string('key')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('sprawdzil_user_id')->nullable();
            $table->timestamp('sprawdzone_at')->nullable();
            $table->unsignedInteger('technik_id');
            $table->unsignedInteger('zlecenie_id');
            $table->timestamp('zlecenie_data')->nullable();
            $table->unsignedInteger('towar_id');
            $table->float('ilosc');
            $table->float('ilosc_do_zwrotu');
            $table->float('ilosc_zamontowane')->default(0);
            $table->float('ilosc_rozpisane')->default(0);
            $table->timestamp('technik_updated_at')->nullable();
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

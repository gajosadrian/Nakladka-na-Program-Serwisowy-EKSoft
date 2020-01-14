<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestZleceniaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('test')->create('test_zlecenia', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zlecenie_id');
            $table->unsignedInteger('user_id');
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
        Schema::dropIfExists('test_zlecenia');
    }
}

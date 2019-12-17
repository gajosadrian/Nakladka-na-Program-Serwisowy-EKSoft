<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZlecenieLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zlecenie_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zlecenie_id');
            $table->unsignedInteger('user_id');
            $table->integer('type');
            $table->timestamp('zlecenie_data')->nullable();
            $table->boolean('terminowo')->default(0);
            $table->text('opis')->nullable();
            $table->unsignedInteger('status_id')->nullable();
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
        Schema::dropIfExists('zlecenie_logs');
    }
}

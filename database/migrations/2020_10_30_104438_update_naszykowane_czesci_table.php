<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNaszykowaneCzesciTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('naszykowane_czesci', function (Blueprint $table) {
            $table->string('opis')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('naszykowane_czesci', function (Blueprint $table) {
            $table->dropColumn('opis');
        });
    }
}

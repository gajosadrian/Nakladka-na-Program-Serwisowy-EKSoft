<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateZlecenieZdjeciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zlecenie_zdjecia', function (Blueprint $table) {
            $table->string('path2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zlecenie_zdjecia', function (Blueprint $table) {
            $table->dropColumn('path2');
        });
    }
}

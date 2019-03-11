<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRozliczeniaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rozliczenia', function (Blueprint $table) {
            $table->string('suma_robocizn', 191)->default('[]')->change();
            $table->string('suma_dojazdow', 191)->default('[]')->change();
        });
        Schema::table('rozliczenia', function (Blueprint $table) {
            $table->renameColumn('suma_robocizn', 'robocizny');
            $table->renameColumn('suma_dojazdow', 'dojazdy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rozliczenia', function (Blueprint $table) {
            $table->unsignedInteger('robocizny')->default(0)->change();
            $table->unsignedInteger('dojazdy')->default(0)->change();
        });
        Schema::table('rozliczenia', function (Blueprint $table) {
            $table->renameColumn('robocizny', 'suma_robocizn');
            $table->renameColumn('dojazdy', 'suma_dojazdow');
        });
    }
}

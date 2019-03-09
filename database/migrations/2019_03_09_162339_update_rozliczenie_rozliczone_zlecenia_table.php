<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRozliczenieRozliczoneZleceniaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rozliczenie_rozliczone_zlecenia', function (Blueprint $table) {
            $table->renameColumn('producent_type', 'zleceniodawca');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rozliczenie_rozliczone_zlecenia', function (Blueprint $table) {
            $table->renameColumn('zleceniodawca', 'producent_type');
        });
    }
}

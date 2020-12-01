<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms', function (Blueprint $table) {
            $table->unsignedInteger('zlecenie_id')->nullable();
            $table->unsignedInteger('zlecenie_status_id')->nullable();
            $table->boolean('auto')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms', function (Blueprint $table) {
            $table->dropColumn('zlecenie_id');
            $table->dropColumn('zlecenie_status_id');
            $table->dropColumn('auto');
        });
    }
}

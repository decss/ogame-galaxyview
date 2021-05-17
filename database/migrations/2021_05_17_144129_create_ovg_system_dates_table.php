<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvgSystemDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ovg_system_dates', function (Blueprint $table) {
            $table->smallInteger('gal')->default(0);
            $table->smallInteger('sys')->default(0);
            $table->dateTime('updated')->useCurrent();

            $table->primary(['gal', 'sys']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ovg_system_dates');
    }
}

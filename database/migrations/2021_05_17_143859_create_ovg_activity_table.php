<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvgActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ovg_activity', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id')->default(0);
            $table->string('coords', 100)->nullable();
            $table->smallInteger('type')->default(0)->comment('1-planet, 2-moon, 3-esp');
            $table->string('date', 16)->nullable();
            $table->string('time', 16)->nullable();
            $table->string('value', 16)->nullable();
            $table->dateTime('created')->useCurrent();

            $table->index('player_id');
            $table->unique(['coords', 'type', 'date', 'time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ovg_activity');
    }
}

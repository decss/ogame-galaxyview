<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvgSystemsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ovg_systems_log', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('gal')->default(0);
            $table->smallInteger('sys')->default(0);
            $table->smallInteger('pos')->default(0);
            $table->integer('player_id')->default(0);
            $table->integer('type')->default(0);
            $table->text('json')->nullable();
            $table->integer('threshold')->default(0);
            $table->dateTime('created')->useCurrent();

            $table->index('type');
            $table->index('created');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ovg_systems_log');
    }
}

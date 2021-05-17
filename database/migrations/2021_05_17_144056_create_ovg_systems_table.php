<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvgSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ovg_systems', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('gal')->default(0);
            $table->smallInteger('sys')->default(0);
            $table->smallInteger('pos')->default(0);
            $table->integer('player_id')->default(0);
            $table->integer('planet_id')->default(0);
            $table->string('planet_name', 255)->nullable();
            $table->string('moon_name', 255)->nullable();
            $table->integer('moon_size')->default(0);
            $table->integer('field_me')->default(0);
            $table->integer('field_cry')->default(0);
            $table->dateTime('updated')->useCurrent();

            $table->index(['gal', 'sys']);
            $table->index('player_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ovg_systems');
    }
}

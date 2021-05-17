<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOgvPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ogv_players', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->integer('ally_id')->default(0);
            $table->integer('rank')->default(0);
            $table->tinyInteger('a')->default(0);
            $table->tinyInteger('o')->default(0);
            $table->tinyInteger('v')->default(0);
            $table->tinyInteger('b')->default(0);
            $table->tinyInteger('i')->default(0)->comment('1-i, 2-I');
            $table->tinyInteger('hp')->default(0);
            $table->dateTime('created')->useCurrent();

            $table->index('ally_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ogv_players');
    }
}

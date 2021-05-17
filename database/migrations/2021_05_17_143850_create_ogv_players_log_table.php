<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOgvPlayersLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ogv_players_log', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id')->default(0);
            $table->smallInteger('type')->default(0);
            $table->text('json')->nullable();
            $table->dateTime('created')->useCurrent();

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
        Schema::dropIfExists('ogv_players_log');
    }
}

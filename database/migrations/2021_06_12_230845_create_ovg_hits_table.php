<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvgHitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ovg_hits', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('type')->default(0)->comment('1-ui, 2-api');
            $table->string('action', 255)->nullable();
            $table->string('ip', 64)->nullable();
            $table->dateTime('created')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ovg_hits');
    }
}

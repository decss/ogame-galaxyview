<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvgSystemApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ovg_system_api', function (Blueprint $table) {
            $table->id();
            $table->integer('system_id')->default(0);
            $table->string('coords', 100)->nullable();
            $table->integer('type')->default(0)->comment('1-planet, 2-moon');
            $table->string('api', 255)->nullable();
            $table->smallInteger('level')->default(0);
            $table->string('res', 255)->nullable();
            $table->string('fleet', 255)->nullable();
            $table->string('def', 255)->nullable();
            $table->dateTime('created')->useCurrent();

            $table->unique(['system_id', 'api']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ovg_system_api');
    }
}

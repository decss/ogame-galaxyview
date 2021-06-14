<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOvgSystemApi2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ovg_system_api', function ($table) {
            $table->dropUnique('ovg_system_api_system_id_api_unique');
            $table->dropColumn('system_id');
            $table->unique(['coords', 'api']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ovg_system_api', function($table) {
            $table->integer('system_id')->default(0)->after('id');
            $table->unique(['system_id', 'api']);
            $table->dropUnique('ovg_system_api_coords_api_unique');
        });
    }
}

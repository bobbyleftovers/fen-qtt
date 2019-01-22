<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfiguredPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lb_entry', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lite_brite_id');
            $table->integer('config_id');
            $table->integer('width');
            $table->integer('height');
            $table->integer('cell_width');
            $table->integer('cell_height');
            $table->json('bulb_data');
            $table->string('json_status');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lb_entry');
    }
}

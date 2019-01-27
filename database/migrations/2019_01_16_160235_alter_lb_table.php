<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lite_brite_images', function (Blueprint $table) {
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('cell_width')->nullable();
            $table->integer('cell_height')->nullable();
            $table->string('cropped_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lite_brite_images', function (Blueprint $table) {
            //
        });
    }
}

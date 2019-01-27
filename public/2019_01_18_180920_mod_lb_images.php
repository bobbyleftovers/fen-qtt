<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModLbImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lite_brite_images', function (Blueprint $table) {
            $table->dropColumn('json_status');
        });
        Schema::rename('lite_brite_images', 'lite_brite_submission');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lite_brite_images');
        Schema::dropIfExists('lite_brite_submission');
    }
}

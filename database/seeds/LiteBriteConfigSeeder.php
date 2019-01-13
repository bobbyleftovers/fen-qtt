<?php

use Illuminate\Database\Seeder;

class LiteBriteConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lite_brite_config')->insert([
            'name' => 'inital',
            'bulb_type' => 'normal',
            'rows' => 10,
            'columns' => 10,
            'dimmer_levels' => 32,
            'is_active' => true,
        ]);
    }
}

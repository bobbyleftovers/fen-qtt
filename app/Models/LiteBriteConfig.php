<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LiteBriteConfig extends Model
{
    public $table = "lite_brite_config";

    // if the site exists in the Websites collection, add it here too
    public function configured(){
        return $this->hasMany('App\Models\LiteBriteImages', 'config_id', 'id');
    }
}
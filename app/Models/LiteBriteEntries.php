<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LiteBriteEntry extends Model
{
    public $table = "lb_entry";

    // if the site exists in the Websites collection, add it here too
    // public function configured(){
        // return $this->hasMany('App\Models\LiteBriteImages', 'config_id', 'id');
    // }
}
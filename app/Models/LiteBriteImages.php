<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\LiteBriteConfig;
class LiteBriteImages extends Model
{
    public $table = "lite_brite_images";
    protected $fillable = ['image_json'];

    // if the site exists in the Websites collection, add it here too
    public function config(){
        return $this->hasOne('App\Models\LiteBriteConfig','id','config_id');
    }
}
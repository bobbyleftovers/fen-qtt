<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\LiteBriteImages;
use App\Models\LiteBriteConfig;
use App\Services\LiteBriteTools;

class ImageConversion extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'image:convert {submission}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Runs a background task to convert an image for the current active liteBrite';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $id = (int) $this->argument('submission');
    $liteBrite = LiteBriteImages::where('id',$id)
      ->first();
    $config = LiteBriteConfig::where('id',$liteBrite->config_id)
      ->first();

    Log::notice('Running image conversion for '.$liteBrite->id. ' with config '.$config->id);
    $this->info('Running image conversion for '.$liteBrite->id. ' with config '.$config->id);
    
    // use lbTools to get the json data and update the entry
    $lbTools = new LiteBriteTools($liteBrite->original_path,$config);
    $lbTools->cropToGrid();
    $liteBrite->update([
      'image_json' => json_encode($lbTools->calculateSegments()),
      'cropped_image' => $lbTools->cropPath,
    ]);

    $this->info($lbTools->getWidth().' x '.$lbTools->getHeight().' '.$lbTools->cropPath);
    $lbTools->cleanup();

    /**
    * Storage::putFileAs('submissions', new File($saved_image_uri),$name);
    * $path = Storage::disk('public')->putFileAs('submissions', new File($saved_image_uri),$name);
    * $url = Storage::disk('public')->url($name);
    */
  }
}

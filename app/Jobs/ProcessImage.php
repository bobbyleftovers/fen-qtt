<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use App\Models\LiteBriteImages;
use App\Models\LiteBriteConfig;
use \App\Services\LiteBriteTools;
use Illuminate\Support\Facades\Log;
use App\Console\Commands\ImageCnversion;

class ProcessImage implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  protected $liteBrite;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(LiteBriteImages $liteBrite)
  {
    $this->liteBrite = $liteBrite;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    Log::notice('Processing Image: '.$this->liteBrite->id);
    Artisan::call('image:convert', ['submission' => $this->liteBrite->id]);
  }
}

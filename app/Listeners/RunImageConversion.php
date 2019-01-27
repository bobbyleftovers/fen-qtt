<?php

namespace App\Listeners;

use App\Events\ImageAdded;
use App\Jobs\ProcessImage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\LiteBriteImages;
use App\Models\LiteBriteConfig;
use \App\Services\LiteBriteTools;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RunImageConversion
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ImageAdded  $event
     * @return void
     */
    public function handle(ImageAdded $event)
    {
        Log::notice('LB Event Listener: Image being added to queue for '.$event->liteBrite->id);
        $job = (new ProcessImage($event->liteBrite));
        dispatch($job);
    }
}

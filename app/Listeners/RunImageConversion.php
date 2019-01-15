<?php

namespace App\Listeners;

use App\Events\ImageAdded;
use App\Models\LiteBriteImages;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

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
        // ProcessImage::dispatch($event);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\LiteBriteImages;
use App\Models\LiteBriteConfig;
use \App\Services\LiteBriteTools;

class ImageConversion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:convert';

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
        // $name = $this->ask('What is your name?');
        $this->info('Running conversion...');
        Log::notice('Running image conversion');
        
    }
}

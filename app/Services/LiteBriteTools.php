<?php
namespace App\Services;

use App\Models\LiteBriteImages;
use App\Models\LiteBriteConfig;
use Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class LiteBriteTools {

	function __construct($path, LiteBriteConfig $config){
		$this->config = $config;
		$this->image = Image::make(Storage::get($path));
		$this->pathinfo = pathinfo($path);
		$this->notice = true;
		$this->croppedImage = null;
		$this->cropPath = '';
		$this->average = [];
		$this->color = [
			'red' => 0,
			'green' => 0,
			'blue' => 0
		];
		$this->colors = [
			'red' => [],
			'green' => [],
			'blue' => []
		];
		$this->average = [
			'red' => 0,
			'green' => 0,
			'blue' => 0
		];
	}

	// Width of $this->image
	function getWidth(){
		return $this->image->height();
	}

	// Height of $this->image
	function getHeight(){
		return $this->image->width();
	}

	// Return the aspect ratio
	function getAspectRatio(){
		return $this->getWidth() / $this->getHeight();
	}

	// Get the cell height
	function getCellHeight(){
        return (int)round($this->getHeight()/$this->config->columns,0,PHP_ROUND_HALF_UP);
	}

	// Get the cell width
	function getCellWidth(){
		return (int)round($this->getWidth()/$this->config->rows,0,PHP_ROUND_HALF_UP);
	}

	// Crop an image to the current configs aspect ratio and return a path to it
	public function cropToGrid(){
		$width = null;
		$height = null;
		
		// set height or width, not both
		($this->config->rows <= $this->config->columns) ? $height = $this->config->columns : $width = $this->config->rows;
		
		// scale image to grid
		$sized = $this->image->resize($width, $height, function($constrain){
			$constrain->aspectRatio();
		});
		
		// crop canvas to grid constraints
		$cropped = $this->image->resizeCanvas($this->config->rows, $this->config->columns, 'center')->save(public_path('images/'.$this->pathinfo['filename'].'.'.$this->pathinfo['extension']));
		
		// uri to temporary resource
		$saved_image_uri = $cropped->dirname.'/'.$cropped->basename;
		
		// remove tem file
		Log::warning('Cropped to aspect ratio: '.$this->cropPath.' '.$saved_image_uri);
		
		// add to storage with naming convention, set cropPath
		$this->cropPath = Storage::putFileAs('submissions', new File($saved_image_uri),$this->pathinfo['filename'].'-config-'.$this->config->id.'.'.$this->pathinfo['extension']);
		unlink($saved_image_uri);
		
		

		return;
	}

	// Copy $this->image and convert to greyscale
	public function setGreyscale(){
		$this->image = $this->image->greyscale();
	}

	public function resetAverage(){
		$this->average = [
			'red' => 0,
			'green' => 0,
			'blue' => 0
		];
		$this->color = [
			'red' => 0,
			'green' => 0,
			'blue' => 0
		];
		$this->colors = [
			'red' => [],
			'green' => [],
			'blue' => []
		];
	}

	// Iterate over sections of a complete image based on a $this->config.
	public function calculateSegments(){
        // set up an object to collect data from this process
		$image_data = [];
		
		$this->croppedImage = Image::make(Storage::get($this->cropPath));
		$this->croppedImage->backup();
		
		// Greyscale operation
		$this->croppedImage->greyscale();

        // dimensions
        $width = $this->croppedImage->width();
        $height = $this->croppedImage->height();

        // crop start pos.
        $xpos = 0;
        $ypos = 0;

        // current slice indeces
        $xslice = 0;
		$yslice = 0;

		// testing, need to get or calc this
		$xslicewidth = 1;
		$ysliceheight = 1;
		

        while($xpos <= ($width - $xslicewidth)){
            while($ypos <= ($height - $ysliceheight)){

				$this->averageSrc($xslicewidth, $ysliceheight, $xpos, $ypos); // add new params here
                $value = [
                    'x' => $xslice,
                    'y' => $yslice,
                    // 'rgb' => $rgb,
                    'grey'=> $this->average['red'],
                    'dimmer' => $this->setDimmerLevel()
				];
				if($this->notice){
					Log::notice('Value set: x->'.$value['x']. ' y->'.$value['y']. ' grey->'.$value['grey']. ' dimmer->'.$value['dimmer']);
				}
				$this->notice=false;
                // reset, increment Y variables
				$this->croppedImage->reset();
				$this->resetAverage();
                $yslice++;
                $ypos = $ysliceheight * $yslice;

                // store data to array
                $image_data[$xslice][] = $value;

			}

            // reset, increment X variables
            $ypos = $yslice = 0;
            $xslice++;
            $xpos = $xslicewidth * $xslice;
		}
		
		Log::notice('Image Processing completed');

        return $image_data;
    }

	// Scan one line of the image, add the pixels to the colors arrays
	function scanLine( $height, $width, $axis, $line)
	{
		if($this->notice){
			Log::notice('Scan: '.$height.' '.$width.' '.$axis.' '.$line);
		}
		$i = 0;
		// Log::notice('if');
		$limit = $width;
		$y = $line;
		$x = $i;
		
		// Log::notice('for2');
		// add all the pixels in the line
		for($i = 0; $i < $limit; $i++){
			$this->addPixel($x, $y);
		}
		if($this->notice){
			Log::notice('line i '.$i);
		}

	}

	// get the rgb values for one pixel and add to the color array
	function addPixel($x, $y)
	{
		$color = $this->image->pickColor($x, $y,'array');
		$this->colors['red'][] = $color[0];
		$this->colors['green'][] = $color[1];
		$this->colors['blue'][] = $color[2];
	}

	function totalColors()
	{
		$this->color['red'] += array_sum($this->colors['red']);
		$this->color['green'] += array_sum($this->colors['green']);
		$this->color['blue'] += array_sum($this->colors['blue']);
		if($this->notice){
			Log::notice('total/size: '.$this->color['red'].' '.sizeof($this->colors['red'])); //sizeof is way too big
		}
	}

	function averageTotal($count)
	{
		$this->average['red'] = intval($this->color['red'] / $count);
		$this->average['green'] = intval($this->color['green'] / $count);
		$this->average['blue'] = intval($this->color['blue'] / $count);
	}

	function averageSrc($xslicewidth, $ysliceheight, $xpos, $ypos) // add new params for this so it can have boundaries within an image
	{
		$width = ($xslicewidth) ? $xslicewidth : $this->getWidth();
		$height = ($ysliceheight) ? $ysliceheight : $this->getHeight();
		// Log::notice('avgSrc: '.$xpos.' '.$ypos.' '.$width.' '.$height);
		
		// $this->scanLine( $height, $width, 'x', 0); // test

		for($line = 0; $line < $height; $line++){
			$this->scanLine( $height, $width, 'x', $line);
			$this->totalColors();
		}
		$count = $width * $height;
		$this->averageTotal($count);
		if($this->notice){
			Log::notice('average total: '.$count.' '.intval($this->average['red'] / $count));
		}
	}

	// Translates a numeric value to correspond to the available dimmer levels
    public function setDimmerLevel(){
		$value = $this->average['red'];
		$interval = 255/$this->config->dimmer_levels;
		$rounded = (int)round($value/$interval,0,PHP_ROUND_HALF_UP);
		if($this->notice){
			Log::notice('set dimmer: '.$value.' '.$interval.' '.$this->config->dimmer_levels.' '.$rounded);
		}
        return $rounded;
	}

	// Destroy the images on the instance (saves memory)
	public function cleanup(){
		$this->image->destroy();
		if($this->croppedImage !== null){
			$this->croppedImage->destroy();
		}
		return;
	}

}
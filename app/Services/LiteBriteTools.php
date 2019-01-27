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
		Log::warning('PATH: '.$path);
		$this->config = $config;
		$this->image = Image::make(Storage::get($path));
		$this->pathinfo = pathinfo($path);
		$this->notice = true;
		$this->croppedImage = null;
		$this->cropPath = '';
		// $this->average = [];
		// $this->color = [
		// 	'red' => 0,
		// 	'green' => 0,
		// 	'blue' => 0
		// ];
		// $this->colors = [
		// 	'red' => [],
		// 	'green' => [],
		// 	'blue' => []
		// ];
		// $this->average = [
		// 	'red' => 0,
		// 	'green' => 0,
		// 	'blue' => 0
		// ];
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
		Storage::disk('public')->putFileAs('submissions', new File($saved_image_uri),$this->pathinfo['filename'].'-config-'.$this->config->id.'.'.$this->pathinfo['extension']);
		// Storage::disk('public')->putFileAs('submissions', new File($saved_image_uri),$name);
        //     $url = Storage::disk('public')->url($name);
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

        // dimensions
        $width = $this->croppedImage->width();
        $height = $this->croppedImage->height();
		
		// go thru each row of pixels in the image
		for($ypos = 0; $ypos < $height; $ypos++){

			// for every pixel in the row, get the rgb value and calc the dimmer value
			for($xpos = 0; $xpos < $width; $xpos++){
				$color = $this->image->pickColor($xpos, $ypos,'array');
				$grey = (int)(($color[0] + $color[1] + $color[2]) / 3);
				$interval = 255/$this->config->dimmer_levels;
				$rounded = (int)round($grey/$interval,0,PHP_ROUND_HALF_UP);
				$value = [
					'x' => $xpos,
					'y' => $ypos,
					'rgb' => $color,
					'grey'=> $grey,
					'dimmer' => $rounded
				];
				$image_data[$xpos][$ypos] = $value;
			}

		}
		// $this->croppedImage->reset();
		
		Log::notice('Image Processing completed');

        return $image_data;
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
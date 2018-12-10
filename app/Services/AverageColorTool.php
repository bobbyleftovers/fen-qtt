<?php
namespace App\Services;

use \App\Models\Content;

class AverageColorTool {

	function __construct($path = '', $resource){
		$this->image = $resource; // the image we're using
		$this->path = $path; // path to the image
		$this->color = array(
			'red' => 0,
			'green' => 0,
			'blue' => 0
		); // the current color
		$this->colors = array(
			'red' => array(),
			'green' => array(),
			'blue' => array()
		); // the sum total of all rgb channels
		$this->count = 0; // tracked throughout and used to find the average a=of all added rgb totals
		$this->average = array(); // the final average color to return
	}

	// Scan one line of the image, add the pixels to the colors arrays
	function scanLine( $height, $width, $axis, $line)
	{
		$i = 0;
		if("x" == $axis){
			$limit = $width;
			$y = $line;
			$x = $i;

			if(-1 == $line){
				$y = 0;
				$y2 = $width -1;
				$x2 =& $i;
			}
		} else {
			$limit = $height;
			$x = $line;
			$y =& $i;

			if(-1 == $line){
				$x = 0;
				$x2 = $width -1;
				$y2 =& $i;
			}
		}
		if(-1 == $line){
			for($i = 0; $i < $limit; $i++){
				$this->addPixel($x, $y);
				$this->addPixel($x2, $y2);
			}
		} else {
			// add all the pixels in the line
			for($i = 0; $i < $limit; $i++){
				$this->addPixel($x, $y);
			}
		}

	}

	function addPixel($x, $y)
	{
		$rgb = imagecolorat($this->image, $x, $y);
		$this->color = imagecolorsforindex($this->image, $rgb);
		$this->colors['red'][] = $this->color['red'];
		$this->colors['green'][] = $this->color['green'];
		$this->colors['blue'][] = $this->color['blue'];
	}

	function totalColors()
	{
		$this->color['red'] += array_sum($this->colors['red']);
		$this->color['green'] += array_sum($this->colors['green']);
		$this->color['blue'] += array_sum($this->colors['blue']);
		// dump($this->color);
	}

	function averageTotal($count)
	{
		$this->average['red'] = intval($this->color['red'] / $count);
		$this->average['green'] = intval($this->color['green'] / $count);
		$this->average['blue'] = intval($this->color['blue'] / $count);
	}

	function averageImage()
	{
		$width = imagesx($this->image);
		$height = imagesy($this->image);
		for($line = 0; $line < $height; $line++){
			$this->scanLine( $height, $width, 'x', $line);
			$this->totalColors();
		}
		dump($width,$height);
		$count = $width * $height;
		$this->averageTotal($count);
	}

}
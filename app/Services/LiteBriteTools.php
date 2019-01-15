<?php
namespace App\Services;

use App\Models\LiteBriteImages;
use App\Models\LiteBriteConfig;
use Intervention\Image;

class LiteBriteTools {

	function __construct(LiteBriteImages $liteBrite, LiteBriteConfig $config){
		$this->liteBrite = $liteBrite;
		$this->config = $config;
		$this->image = Image::make('images/'.$liteBrite->filename); // the image we're using
		$this->pathinfo = pathinfo('images'.$liteBrite->filename);
		$this->config = $config;
		$this->color = array(
			'red' => 0,
			'green' => 0,
			'blue' => 0
		);
		// the current color
		$this->colors = array(
			'red' => array(),
			'green' => array(),
			'blue' => array()
		); // the sum total of all rgb channels
		$this->count = 0; // tracked throughout and used to find the average of all added rgb totals
		$this->average = array(); // the final average color to return

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

	// Iterate over sections of a complete image based on a $this->config.
	public function calculateSegments(){
        
        // set up an object to collect data from this process
        $image_data = [];
        
        $this->image->backup();

        // dimensions
        $width = $this->getWidth();
        $height = $this->getHeight();
        $rows = $this->config->rows;
        $columns = $this->config->columns;

        // crop the image to match the grids aspect ratio
        // $this->image->crop($xslicewidth, $ysliceheight, $xpos, $ypos);

        // crop start pos.
        $xpos = 0;
        $ypos = 0;

        // current slice indeces
        $xslice = 0;
        $yslice = 0;

        // set crop size
        $xslicewidth = (int)round($width/$rows,0,PHP_ROUND_HALF_UP);
        $ysliceheight = (int)round($height/$columns,0,PHP_ROUND_HALF_UP);

        while($xpos <= ($width - $xslicewidth)){
            while($ypos <= ($height - $ysliceheight)){
                
                // we'll be storing the data in here
                $value = [];
                $filepath = 'images/'.$pathinfo['filename'].'-'.$xpos.'-'.$ypos.'.jpg';

                // crop image
                //$this->image->crop($xslicewidth, $ysliceheight, $xpos, $ypos); // get rid of this
                //$this->image->save($filepath);
                
                // get average color
                $src = imagecreatefromjpeg($filepath);

                // Greyscale operation
                imagecopymergegray($src, $src, 0, 0, 0, 0, imagesx($src), imagesy($src), 0);
                
                // $avgImage = new AverageColorTool('',$src);
                $rgb = $this->averageImage(); // add new params here
                $value = [
                    'x' => $xslice,
                    'y' => $yslice,
                    'rgb' => $rgb,
                    'grey'=> $rgb['red'],
                    'dimmer' => $this->setDimmerLevel($rgb['red'],$config)
                ];

                // get rid of the extra file
                unlink($filepath);

                // reset, increment Y variables
                $this->image->reset();
                $yslice++;
                $ypos = $ysliceheight * $yslice;

                // store data to array
                $image_data['values'][$xslice][] = $value;

            }

            // reset, increment X variables
            $ypos = $yslice = 0;
            $xslice++;
            $xpos = $xslicewidth * $xslice;
        }

        return $image_data;
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

	function averageImage() // add new params for this so it can have boundaries within an image
	{
		
		$width = imagesx($this->image);
		$height = imagesy($this->image);

		for($line = 0; $line < $height; $line++){
			$this->scanLine( $height, $width, 'x', $line);
			$this->totalColors();
		}
		$count = $width * $height;
		$this->averageTotal($count);

		// returns rgb array
		return $this->average;
	}

	// Translates a numeric value to correspond to the available dimmer levels
    public function setDimmerLevel($value,$config){
        $interval = 255/$config->dimmer_levels;
        return (int)round($value/$interval,0,PHP_ROUND_HALF_UP);
	}
	
	/**
     * Copy an image
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function avgColor($source)
    {
        dump($source);
        $i = imagecreatefromjpeg($source);
        for ($x=0;$x<imagesx($i);$x++) {
            for ($y=0;$y<imagesy($i);$y++) {
                $rgb = imagecolorat($i,$x,$y);
                $r   = ($rgb >> 16) & 0xFF;
                $g   = $rgb & 0xFF;
                $b   = $rgb & 0xFF;
                $rTotal += $r;
                $gTotal += $g;
                $bTotal += $b;
                $total++;
            }
        }
        $rAverage = round($rTotal/$total);
        $gAverage = round($gTotal/$total);
        $bAverage = round($bTotal/$total);
        return $data;
    }

}
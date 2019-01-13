<?php

namespace App\Http\Controllers;

use App\Models\LiteBriteImages;
use App\Models\LiteBriteConfig;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use \App\Services\AverageColorTool;
use Ixudra\Curl\Facades\Curl;
use CurlFile;
use Image;

class LiteBrite extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('main');
    }

    public function entries(){
        // get submissions
        $items = LiteBriteImages::with('config')
        ->get()
        ->toJson();
        return $items;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        // get the current active grid config
        $config = LiteBriteConfig::where('is_active',1)
            ->first();

        if($request->get('file')){
            $info = $request->get('info');
            $image = $request->get('file');
            $name = $info['full_name'];
            \Image::make($request->get('file'))->save(public_path('images/').$name);

            $liteBrite = new LiteBriteImages;
            $liteBrite->config_id = $config->id;
            $liteBrite->filename = $name;
            $liteBrite->save();
            $liteBrite->update([
                'image_json' =>  json_encode($this->calculate($liteBrite,$config))
            ]);
            
            return response()->json(['filename' => $name, 'imageData' => $liteBrite->image_json]);
        }
        return false;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {   
        // $response = Curl::to($request->get('url'))
        // ->withFile( 'file', $request->get('path'), 'image/png', 'imageName1.png' )
        // ->withData( array( 'test' => 'Bar' ) )
        // ->post();

        // return response()->json([$request->get('path'),$response]);
        return;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('main');
    }

    /**
     * Get the specified resource (async).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getImage(Request $request)
    {
        $item = LiteBriteImages::with('config')->find($request->get('id'))
        ->toJson();
        return $item;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $config = LiteBriteConfig::where('is_active',1)
            ->first();
        
        $liteBrite = LiteBriteImages::where('id',$request->get('id'))
            ->first();
        return response()->json($this->calculate($liteBrite,$config));
        $liteBrite->update([
            'image_json' =>  json_encode($this->calculate($liteBrite,$config))
        ]);
        return response()->json($liteBrite);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // UTILITIES

    public function calculate($liteBrite,$config){
        
        // set up an object to collect data from this process
        $image_data = [];
        $pathinfo = pathinfo('images'.$liteBrite->filename);
        
        // open file a image resource
        $img = Image::make('images/'.$liteBrite->filename);
        $img->backup();

        // dimensions
        $width = $img->width();
        $height = $img->height();
        $rows = $config->rows;
        $columns = $config->columns;

        // crop start pos.
        $xpos = 0;
        $ypos = 0;

        // current slice indeces
        $xslice = 0;
        $yslice = 0;

        // set crop size
        $xslicewidth = (int)round($width/$rows,0,PHP_ROUND_HALF_UP);
        $ysliceheight = (int)round($height/$columns,0,PHP_ROUND_HALF_UP);

        $image_data['image'] = [
            'id' => $liteBrite->id,
            'config_id' => $config->id,
            'width' => $width,
            'height' => $height,
            'cellHeight' => $ysliceheight,
            'cellWidth' => $xslicewidth
        ];

        while($xpos <= ($width - $xslicewidth)){
            while($ypos <= ($height - $ysliceheight)){
                
                // we'll be storing the data in here
                $value = [];
                $filepath = 'images/'.$pathinfo['filename'].'-'.$xpos.'-'.$ypos.'.jpg';

                // crop image
                $img->crop($xslicewidth, $ysliceheight, $xpos, $ypos);
                $img->save($filepath);
                
                // get average color
                $src = imagecreatefromjpeg($filepath);

                // Greyscale operation
                imagecopymergegray($src, $src, 0, 0, 0, 0, imagesx($src), imagesy($src), 0);
                
                $avgImage = new AverageColorTool('',$src);
                $rgb = $avgImage->averageImage();
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
                $img->reset();
                $yslice++;
                $ypos = $ysliceheight * $yslice;

                // store data to array
                $image_data['values'][$xslice][$yslice] = $value;

            }

            // reset, increment X variables
            $ypos = $yslice = 0;
            $xslice++;
            $xpos = $xslicewidth * $xslice;
        }

        return $image_data;
    }

    // Translates a numeric value to correspond to the available dimmer levels
    public function setDimmerLevel($value,$config){
        $interval = 255/$config->dimmer_levels;
        return (int)round($value/$interval,0,PHP_ROUND_HALF_UP);
    }

    public function start_php()
    {

       // get an image and config to test calculate with
       $liteBrite = LiteBriteImages::find(1)->first();
       $config = LiteBriteConfig::find(1)->first();
       $this->calculate($liteBrite,$config);



        return view('main');

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

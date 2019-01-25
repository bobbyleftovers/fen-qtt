<?php

namespace App\Http\Controllers;

use App\Models\LiteBriteImages;
use App\Models\LiteBriteConfig;
use App\Events\ImageAdded;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use \App\Services\LiteBriteTools;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;
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

        // make sure we have a file
        if($request->get('file')){

            // save the image using intervention/image
            $info = $request->get('info');;
            $name = $info['full_name'];
            $upload = Image::make($request->get('file'));
            $upload->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('images/'.$name));
            $saved_image_uri = $upload->dirname.'/'.$upload->basename;
            
            // not sure which will be best, so for now store in two places:
            Storage::disk('public')->putFileAs('submissions', new File($saved_image_uri),$name); // to public directory for frontend
            $path = Storage::putFileAs('submissions', new File($saved_image_uri),$name); // for backend manipulation
            $url = Storage::disk('public')->url($name);
            Log::notice($path.' '.$url);
            
            // set up the liteBrite entry and save
            $liteBrite = new LiteBriteImages;
            $liteBrite->config_id = $config->id;
            $liteBrite->filename = $info['name'];
            $liteBrite->original_path = $path; // '/storage/'.$path;
            $liteBrite->save();

            // clean up intervention stuff
            $upload->destroy();
            unlink($saved_image_uri);

            // Log it out 
            Log::notice('LiteBrite entry created: '.$liteBrite->id.', JSON pending. Config ID is '.$config->id);

            // Emit json event
            event(new ImageAdded($liteBrite));

            return response()->json($liteBrite);
        }

        // if no file, return false
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
            
        $lb = new LiteBriteTools($liteBrite,$config);
        LiteBriteImages::where('id',$request->get('id'))
            ->update([
                'config_id' => $config->id,
                'json_status' => 'pending',
            ]);

        

        // Log it out 
        Log::info('LiteBrite entry updated:'.$liteBrite->id.', JSON pending. Config ID is '.$config->id);

        // start the rebuild process if needed and emit json event
        // event(new ImageAdded($liteBrite));
        // $segments = $lb->calculateSegments();
        $lb->cleanup();
        return response()->json($segments);
    }

    // Update the user on wether the json build has finished
    public function check_json_status($request){
        $liteBrite = LiteBriteImages::where('id', $request->get('id'))->first();
        return response()->json($liteBrite->json_status); // not tested yet
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

    // public function calculate(LiteBriteImages $liteBrite, LiteBriteConfig $config){
        
    //     // set up an object to collect data from this process
    //     $image_data = [];
    //     $pathinfo = pathinfo('images'.$liteBrite->filename);
        
    //     // open file a image resource
    //     $img = Image::make('images/'.$liteBrite->filename);
        
    //     $img->backup();

    //     // dimensions
    //     $width = $img->width();
    //     $height = $img->height();
    //     $rows = $config->rows;
    //     $columns = $config->columns;
    //     $aspect_ratio = $rows/$columns;

    //     // crop the image to match the grids aspect ratio
    //     // $img->crop($xslicewidth, $ysliceheight, $xpos, $ypos);

    //     // crop start pos.
    //     $xpos = 0;
    //     $ypos = 0;

    //     // current slice indeces
    //     $xslice = 0;
    //     $yslice = 0;

    //     // set crop size
    //     $xslicewidth = (int)round($width/$rows,0,PHP_ROUND_HALF_UP);
    //     $ysliceheight = (int)round($height/$columns,0,PHP_ROUND_HALF_UP);

    //     // move this to store method and create new columns, json should only be for the values
    //     $image_data['image'] = [
    //         'id' => $liteBrite->id,
    //         'config_id' => $config->id,
    //         'width' => $width,
    //         'height' => $height,
    //         'cellHeight' => $ysliceheight,
    //         'cellWidth' => $xslicewidth
    //     ];

    //     while($xpos <= ($width - $xslicewidth)){
    //         while($ypos <= ($height - $ysliceheight)){
                
    //             // we'll be storing the data in here
    //             $value = [];
    //             $filepath = 'images/'.$pathinfo['filename'].'-'.$xpos.'-'.$ypos.'.jpg';

    //             // crop image
    //             $img->crop($xslicewidth, $ysliceheight, $xpos, $ypos); // get rid of this
    //             $img->save($filepath);
                
    //             // get average color
    //             $src = imagecreatefromjpeg($filepath);

    //             // Greyscale operation
    //             imagecopymergegray($src, $src, 0, 0, 0, 0, imagesx($src), imagesy($src), 0);
                
    //             $avgImage = new AverageColorTool('',$src);
    //             $rgb = $avgImage->averageImage();
    //             $value = [
    //                 'x' => $xslice,
    //                 'y' => $yslice,
    //                 'rgb' => $rgb,
    //                 'grey'=> $rgb['red'],
    //                 'dimmer' => $this->setDimmerLevel($rgb['red'],$config)
    //             ];

    //             // get rid of the extra file
    //             unlink($filepath);

    //             // reset, increment Y variables
    //             $img->reset();
    //             $yslice++;
    //             $ypos = $ysliceheight * $yslice;

    //             // store data to array
    //             $image_data['values'][$xslice][] = $value;

    //         }

    //         // reset, increment X variables
    //         $ypos = $yslice = 0;
    //         $xslice++;
    //         $xpos = $xslicewidth * $xslice;
    //     }

    //     return $image_data;
    // }

    public function start_php()
    {

       // get an image and config to test calculate with
       $liteBrite = LiteBriteImages::find(1)->first();
       $config = LiteBriteConfig::find(1)->first();
       $this->calculate($liteBrite,$config);



        return view('main');

    }

    
}
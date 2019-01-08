<?php

namespace App\Http\Controllers;

use App\LiteBriteImage;
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
        $items = LiteBriteImage::all()
        // ->with('config')
        ->get()
        ->toArray();
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
        if($request->get('file')){

            $image = $request->get('file');
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            \Image::make($request->get('file'))->save(public_path('images/').$name);
            $new_image = '/Users/robertrae/Sites/Enjoy/fen-qtt-server/public/images/'.$name;
        
        }
        
        return response()->json(['path' => $new_image, 'name' => $name]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {   
        $response = Curl::to($request->get('url'))
        ->withFile( 'file', $request->get('path'), 'image/png', 'imageName1.png' )
        ->withData( array( 'test' => 'Bar' ) )
        ->post();

        return response()->json([$request->get('path'),$response]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $items = LiteBriteImage::with('config')->find($id)
        ->toArray();
        return view('main');
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
    public function update(Request $request, $id)
    {
        //
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

    public function start_php()
    {

        // set up an object to collect data from this process
        $image_data = [
            'image' => '',
            'values' => []
        ];

        // test dimensions
        $width = 720;
        $height = 540;
        
        // test crop start pos.
        $xpos = 0;
        $ypos = 0;

        // test slice index
        $xslice = 0;
        $yslice = 0;

        // test crop size
        $xslicewidth = 720/10;
        $ysliceheight = 540/10;

        // Create image instances
        $src = imagecreatefromjpeg('images/david_bowie.jpg');

        // open file a image resource
        $img = Image::make('images/david_bowie.jpg');
        $img->backup();
        while($xpos <= ($width - $xslicewidth)){
            while($ypos <= ($height - $ysliceheight)){

                // crop image
                $img->crop($xslicewidth, $ysliceheight, $xpos, $ypos);
                $img->save('images/test-'.$xpos.'-'.$ypos.'.jpg');
                
                // get average color
                $src = imagecreatefromjpeg('images/test-'.$xpos.'-'.$ypos.'.jpg');

                // Greyscale operation
                imagecopymergegray($src, $src, 0, 0, 0, 0, imagesx($src), imagesy($src), 0);
                
                $avgImage = new AverageColorTool('',$src);
                $image_data['values'][$xslice][$yslice] = $avgImage->averageImage();
                // dump($xslice,$yslice,$test,'-');

                // get rid of the extra file
                unlink('images/test-'.$xpos.'-'.$ypos.'.jpg');

                // reset, increment Y variables
                $img->reset();
                $yslice++;
                $ypos = $ysliceheight * $yslice;

            }

            // reset, increment X variables
            $ypos = $yslice = 0;
            $xslice++;
            $xpos = $xslicewidth * $xslice;
            // dump('---');
        }

        dump($image_data);

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

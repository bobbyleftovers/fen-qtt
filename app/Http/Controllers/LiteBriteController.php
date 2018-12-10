<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Services\AverageColorTool;

class LiteBriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'liteBrite';
        $page_slug = 'liteBrite';
        return view('litebrite/litebrite-home',compact('liteBrite','page_title','page_slug'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Lite Brite';
        $page_slug = 'liteBrite';
        return view('litebrite/litebrite-home');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $image = $request->file('image');

        $input['imagename'] = time().'.'.$image->getClientOriginalExtension();

        $destinationPath = public_path('/images');

        $image->move($destinationPath, $input['imagename']);

        $this->postImage->add($input);

        return view('litebrite/litebrite-store');
    }

    public function fileUpload(Request $request)
    {

        // $this->validate($request, [

        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        // ]);


        // $image = $request->file('image');

        $input['imagename'] = time().'.'.$image->getClientOriginalExtension();

        $destinationPath = public_path('/images');

        $image->move($destinationPath, $input['imagename']);


        $this->postImage->add($input);


        return back()->with('success','Image Upload successful');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Lite Brite';
        $page_slug = 'liteBrite';
        return view('litebrite-results','id');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Lite Brite';
        $page_slug = 'liteBrite';
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

    /**
     * Get Current Config for LieBrite
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getConfig()
    {
        return view('litebrite/litebrite-config');
    }

    /**
     * Set Current Config for LieBrite
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setConfig()
    {
        $page_title = 'Lite Brite';
        $page_slug = 'liteBrite';
        return view('litebrite/litebrite-config');
    }

    /**
     * PHP Version
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function start_php()
    {

        // Create image instances
        $src = imagecreatefromjpeg('images/david_bowie.jpg');
        $src2 = imagecreatefromjpeg('images/db_face2.jpg');
        $dest = imagecreatefromjpeg('images/david_bowie.jpg');
        $test = new AverageColorTool('',$src2);
        // $test2 = new Imagick('http://enjoy.dev/images/david_bowie.jpg');
        // dump($test2);
        $test->averageImage();

        // Greyscale operation
        imagecopymergegray($dest, $src, 0, 0, 0, 0, imagesx($src), imagesy($src), 0);

        // Save the greyscale image file
        imagePNG($dest, 'images/mygreyscaleimage.png', 0);
        $info = getimagesize('images/mygreyscaleimage.png');
        // dump($info);

        // Remove original images from memory.
        imagedestroy($dest);
        imagedestroy($src);

        return view('litebrite/litebrite-phpversion');

    }

    /**
     * Copy an image
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function avgColor($source)
    {
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

    /**
     * Perform a simple test of the mosquitto messenger
     * @return message and other data from mosquitto response
     */
    public function mqqtTest(){
        // send out a simple message, a request for info from the rasperry pi
        $res = null;

        // do some housekeeping

        // return the data
        return $res;
    }
}
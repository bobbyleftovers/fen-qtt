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

    public function entries()
    {
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
        $config = LiteBriteConfig::where('is_active', 1)
            ->first();

        // make sure we have a file
        if ($request->get('file')) {

            // save the image using intervention/image
            $info = $request->get('info');;
            $name = $info['full_name'];
            $upload = Image::make($request->get('file'));
            $upload->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('images/' . $name));
            $saved_image_uri = $upload->dirname . '/' . $upload->basename;
            
            // not sure which will be best, so for now store in two places:
            Storage::disk('public')->putFileAs('submissions', new File($saved_image_uri), $name); // to public directory for frontend
            $path = Storage::putFileAs('submissions', new File($saved_image_uri), $name); // for backend manipulation
            $url = Storage::disk('public')->url($name);
            Log::notice($path . ' ' . $url);
            
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
            Log::notice('LiteBrite entry created: ' . $liteBrite->id . ', JSON pending. Config ID is ' . $config->id);

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

        Log::info('Uploader hit');

        // get config
        $config = LiteBriteConfig::where('is_active', 1)
            ->first();

        $name = $request['filename'];
        $upload = Image::make($request->get('base64'));
        $upload->resize(600, 600, function ($constraint) {
            $constraint->aspectRatio();
        })->save(public_path('images/' . $name));
        $saved_image_uri = $upload->dirname . '/' . $upload->basename;

        // not sure which will be best, so for now store in two places:
        Storage::disk('public')->putFileAs('submissions', new File($saved_image_uri), $name); // to public directory for frontend
        $path = Storage::putFileAs('submissions', new File($saved_image_uri), $name); // for backend manipulation
        $url = Storage::disk('public')->url($name);
        Log::notice($path . ' ' . $url);
            
        // set up the liteBrite entry and save
        $liteBrite = new LiteBriteImages;
        $liteBrite->config_id = $config->id;
        $liteBrite->filename = $request['filename'];
        $liteBrite->original_path = $path; // '/storage/'.$path;
        $liteBrite->save();

        // clean up intervention stuff
        $upload->destroy();
        unlink($saved_image_uri);

        // Log it out 
        Log::notice('LiteBrite entry created: ' . $liteBrite->id . ', JSON pending. Config ID is ' . $config->id);

        // Emit json event
        event(new ImageAdded($liteBrite));

        return response()->json($liteBrite);
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
        $config = LiteBriteConfig::where('is_active', 1)
            ->first();

        $liteBrite = LiteBriteImages::where('id', $request->get('id'))
            ->first();

        $lb = new LiteBriteTools($liteBrite->original_path, $config);
        LiteBriteImages::where('id', $request->get('id'))
            ->update([
                'config_id' => $config->id,
                'json_status' => 'pending',
            ]);

        // Log it out 
        Log::info('LiteBrite entry updated:' . $liteBrite->id . ', JSON pending. Config ID is ' . $config->id);

        // start the rebuild process if needed and emit json event
        event(new ImageAdded($liteBrite));
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

    // API
    public function apigettest()
    {
        return response()->json(['one', 'two', 'three']);
    }
    public function apiposttest(Request $request)
    {
        return response()->json([$request['id']]);
    }
    // Update the user on wether the json build has finished
    public function check_json_status($request)
    {
        $liteBrite = LiteBriteImages::where('id', $request->get('id'))->first();
        return response()->json($liteBrite->json_status); // not tested yet
    }
    public function getJsonImage(Request $request)
    {
        $image = LiteBriteImages::with('config')
            ->where('id', $request['id'])
            ->first();
        if (!is_object($image)) {
            return response()->json(['error' => 'No resources found']);
        }
        return response()->json(['json' => $image->id]);
    }

}

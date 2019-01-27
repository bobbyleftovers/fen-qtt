<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiteBriteConfig;

class LiteBriteConfigController extends Controller
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
     * Get a resource by id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getConfig(Request $request)
    {   
        $id = $request->get('id');
        $config = LiteBriteConfig::where('id',$id)
        ->first();
        $config->is_active = ($config->is_active == 1) ? true : false;
        return response()->json($config);
    }

    public function getActiveConfig()
    {   
        $config = LiteBriteConfig::where('is_active',1)
        ->first();
        return response()->json($config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->get('is_active') == true){
            $this->deactivateAll();
        }

        $config = new LiteBriteConfig;
        $config->rows = $request->get('rows');
        $config->columns = $request->get('columns');
        $config->dimmer_levels = $request->get('dimmer_levels');
        $config->name = $request->get('config_name');
        $config->bulb_type = $request->get('bulb_type');
        $config->is_active = $request->get('is_active');
        $config->save();
        
        return response()->json($config);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        if($request->get('is_active') == true){
            $this->deactivateAll();
        }

        LiteBriteConfig::where('id',$request->get('id'))
            ->update([
                'rows' => $request->get('rows'),
                'columns' => $request->get('columns'),
                'dimmer_levels' => $request->get('dimmer_levels'),
                'name' => $request->get('config_name'),
                'bulb_type' => $request->get('bulb_type'),
                'is_active' => $request->get('is_active')
            ]);
            
        return 'ok';
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

    // A function to deactivate all other configs
    public function deactivateAll(){
        $config = LiteBriteConfig::where('is_active','=',true)->update(['is_active' => false]);
        return;
    }
}

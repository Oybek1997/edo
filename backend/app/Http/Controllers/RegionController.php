<?php

namespace App\Http\Controllers;

use App\Http\Models\Country;
use App\Http\Models\Region;
use App\Http\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ['region'=> Region::with('country')->get(),'country'=>Country::get()];
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\regions  $regions
     * @return \Illuminate\Http\Response
     */
    public function show(regions $regions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\regions  $regions
     * @return \Illuminate\Http\Response
     */
    public function edit(regions $regions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\regions  $regions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = Region::find($request->input('id'));
        if (!$model) {
            $model = new Region();
            $model->created_by= Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->country_id = $request['country_id'];
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\regions  $regions
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Region::find($id);
        $model->delete();
    }

    public function just()
    {
        $document = Document::with('documentDetails')->with('documentDetails.documentDetailAttributeValues')->find(27120);
        return $document->status;
    }
}

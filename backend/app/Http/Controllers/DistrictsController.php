<?php

namespace App\Http\Controllers;

use App\Http\Models\District;
use App\Http\Models\districts;
use App\Http\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistrictsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ['districts'=> District::with('region')->get(),'regions'=>Region::get()];
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
     * @param  \App\Http\Models\districts  $districts
     * @return \Illuminate\Http\Response
     */
    public function show(districts $districts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\districts  $districts
     * @return \Illuminate\Http\Response
     */
    public function edit(districts $districts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\districts  $districts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = District::find($request->input('id'));
        if (!$model) {
            $model = new District();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }

        $model->region_id = $request['region_id'];
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\districts  $districts
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = District::find($id);
        $model->delete();
    }
}

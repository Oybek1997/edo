<?php

namespace App\Http\Controllers;

use App\Http\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Country::get();
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
     * @param  \App\Http\Models\countries  $countries
     * @return \Illuminate\Http\Response
     */
    public function show(countries $countries)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\countries  $countries
     * @return \Illuminate\Http\Response
     */
    public function edit(countries $countries)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\countries  $countries
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, countries $countries)
    {
        $model = Country::find($request->input('id'));
        if (!$model) {
            $model = new Country();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }

        $model->country_code = $request['country_code'];
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\countries  $countries
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Country::find($id);
        $model->delete();
    }
}

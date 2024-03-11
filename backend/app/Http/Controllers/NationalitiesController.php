<?php

namespace App\Http\Controllers;

use App\Http\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NationalitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Nationality::get();
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
     * @param  \App\Http\Models\nationalities  $nationalities
     * @return \Illuminate\Http\Response
     */
    public function show(nationalities $nationalities)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\nationalities  $nationalities
     * @return \Illuminate\Http\Response
     */
    public function edit(nationalities $nationalities)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\nationalities  $nationalities
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = Nationality::find($request->input('id'));
        if (!$model) {
            $model = new Nationality();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\nationalities  $nationalities
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Nationality::find($id);
        $model->delete();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Models\HrStateAward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrStateAwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return HrStateAward::all();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(HrStateAward $HrStateAward)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(HrStateAward $HrStateAward)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\HrStateAward $HrStateAward
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HrStateAward $HrStateAward)
    {
        //
        $model = HrStateAward::find($request->input('id'));

        if(!$model){
            $model = new HrStateAward();
            $model->created_by=Auth::id();
        }
        else {
	        $model->updated_by = Auth::id();
        }

        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        // $model->updated_by = Auth::id();
        $model->save();

        return HrStateAward::find($model->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\HrStateAward $HrStateAward
     * @return \Illuminate\Http\Response
     */
    public function destroy(HrStateAward $HrStateAward, $id)
    {
        //
        $model = HrStateAward::find($id);
        $model->delete();
    }
}

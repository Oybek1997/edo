<?php

namespace App\Http\Controllers;

use App\Http\Models\HrMilitaryRank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrMilitaryRankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return HrMilitaryRank::all();
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
    public function show(HrMilitaryRank $HrMilitaryRank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(HrMilitaryRank $HrMilitaryRank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\HrMilitaryRank $HrMilitaryRank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HrMilitaryRank $HrMilitaryRank)
    {
        //
        $model = HrMilitaryRank::find($request->input('id'));

        if(!$model){
            $model = new HrMilitaryRank();
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

        return HrMilitaryRank::find($model->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\HrMilitaryRank $HrMilitaryRank
     * @return \Illuminate\Http\Response
     */
    public function destroy(HrMilitaryRank $HrMilitaryRank, $id)
    {
        //
        $model = HrMilitaryRank::find($id);
        $model->delete();
    }
}

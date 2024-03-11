<?php

namespace App\Http\Controllers;

use App\Http\Models\HrStudyDegree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrStudyDegreeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return HrStudyDegree::all();
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
    public function show(HrStudyDegree $HrStudyDegree)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(HrStudyDegree $HrStudyDegree)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\HrStudyDegree $HrStudyDegree
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HrStudyDegree $HrStudyDegree)
    {
        //
        $model = HrStudyDegree::find($request->input('id'));

        if(!$model){
            $model = new HrStudyDegree();
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

        return HrStudyDegree::find($model->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\HrStudyDegree $HrStudyDegree
     * @return \Illuminate\Http\Response
     */
    public function destroy(HrStudyDegree $HrStudyDegree, $id)
    {
        //
        $model = HrStudyDegree::find($id);
        $model->delete();
    }
}

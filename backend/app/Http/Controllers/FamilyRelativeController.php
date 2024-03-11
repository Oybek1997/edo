<?php

namespace App\Http\Controllers;

use App\Http\Models\FamilyRelative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamilyRelativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return FamilyRelative::all();
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
     * @param  \App\Http\Models\FamilyRelative  $familyRelative
     * @return \Illuminate\Http\Response
     */
    public function show(FamilyRelative $familyRelative)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\FamilyRelative  $familyRelative
     * @return \Illuminate\Http\Response
     */
    public function edit(FamilyRelative $FamilyRelative)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\FamilyRelative  $familyRelative
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FamilyRelative $familyRelative)
    {
        //
        $model = FamilyRelative::find($request->input('id'));
        // return $model;

        if (!$model) {
            $model = new FamilyRelative();
	        $model->created_by= Auth::id();
        }
        
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->status = $request['status'];
        $model->updated_by = Auth::id();
        $model->save();

        return FamilyRelative::find($model->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\FamilyRelative  $familyRelative
     * @return \Illuminate\Http\Response
     */
    public function destroy(FamilyRelative $familyRelative, $id)
    {
        //
        $model = FamilyRelative::find($id);
        $model->delete();
    }
}

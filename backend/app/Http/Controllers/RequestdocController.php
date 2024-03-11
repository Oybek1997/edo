<?php

namespace App\Http\Controllers;

use App\Http\Models\Requestdoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestdocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Requestdoc::all();
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
     * @param  \App\Http\Models\Requestdoc  $Requestdoc
     * @return \Illuminate\Http\Response
     */
    public function show(Requestdoc $Requestdoc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Requestdoc  $Requestdoc
     * @return \Illuminate\Http\Response
     */
    public function edit(Requestdoc $Requestdoc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Requestdoc  $Requestdoc
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Requestdoc $Requestdoc)
    {
        //
        $model = Requestdoc::find($request->input('id'));

        if(!$model){
            $model = new Requestdoc;
            $model->created_by = Auth::id();
        } else{
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
     * @param  \App\Http\Models\Requestdoc  $Requestdoc
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requestdoc $Requestdoc, $id)
    {
        //
        $model = Requestdoc::find($id);
        $model->delete();
    }
}

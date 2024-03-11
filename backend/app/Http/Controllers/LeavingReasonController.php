<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\LeavingReason;
use App\Http\Controllers\LeavingReasonController;

class LeavingReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(LeavingReason::get(), 200);
        // return LeavingReason::get();
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
        $leavingReason = LeavingReason::create($request->all());
        return response()->json(LeavingReason::get(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leaving_reason = LeavingReason::find($id);
        if(is_null($leaving_reason)){
            return response()->json(["message" => "Ma`lumot topilmadi"], 404);
        }

        return response()->json(LeavingReason::find($id), 200);
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
    public function update(Request $request, LeavingReason $leavingReason)
    {
        //
        $model = LeavingReason::find($request->input('id'));
        if (!$model) {
            $model = new LeavingReason();
        // $model->created_by= Auth::id();
        } else {
            // $model->updated_by = Auth::id();
        }
        $model->name_uz_latin = $request['name_uz_latin'];
        $model->name_uz_cyril = $request['name_uz_cyril'];
        $model->name_ru = $request['name_ru'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeavingReason $leavingReason, $id)
    {
        $model = LeavingReason::find($id);
        $model->delete();
        // return response()->json(null, 204);
    }
}

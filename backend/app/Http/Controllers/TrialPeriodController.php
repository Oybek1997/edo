<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrialPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TrialPeriod::with('employee')
        ->get();
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
    public function show($id)
    {
        //
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
    public function update(Request $request, TrialPeriod $TrialPeriod)
    {
        $model = TrialPeriod::find($request->input('id'));
        if (!$model) {
            $model = new TrialPeriod();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->department_code = $request['department_code'];
        $model->employee_department = $request['employee_department'];
        $model->employee_position = $request['employee_position'];
        $model->description = $request['description'];
        return $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrialPeriod $TrialPeriod, $id)
    {
        $model = TrialPeriod::find($id);
        $model->delete();
    }
}

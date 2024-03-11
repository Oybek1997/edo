<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeWorkHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeWorkHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmployeeWorkHistory::with('employee')->get();
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
     * @param  \App\EmployeeWorkHistory  $employeeWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeWorkHistory $employeeWorkHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeWorkHistory  $employeeWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeWorkHistory $employeeWorkHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeWorkHistory  $employeeWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = EmployeeWorkHistory::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeWorkHistory();
            $model->created_by= Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->begin_date = $request['begin_date'];
        $model->end_date = $request['end_date'];
        $model->work_place = $request['work_place'];
        $model->position = $request['position'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeWorkHistory  $employeeWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        EmployeeWorkHistory::find($id)->delete();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeTraningTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EmployeeTraningTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $tasks = EmployeeTraningTask::with('comments')->with('file')->where('employee_id', $id)->get();
        return $tasks;
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
     * @param  \App\Http\Models\EmployeeTraningTask $employeeTraningTask
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeTraningTask $employeeTraningTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeTraningTask $employeeTraningTask
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeTraningTask $employeeTraningTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EmployeeTraningTask $employeeTraningTask
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeTraningTask $employeeTraningTask)
    {
        //
        $model = EmployeeTraningTask::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeTraningTask();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->begin_date = $request['begin_date'];
        $model->due_date = $request['due_date'];
        $model->task = $request['task'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EmployeeTraningTask $employeeTraningTask
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = EmployeeTraningTask::find($id);
        $model->delete();
    }
}

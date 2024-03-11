<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeEducationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeEducationHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmployeeEducationHistory::with('employee')->with('studyType')->get();
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
     * @param  \App\EmployeeEducationHistory  $employeeEducationHistory
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeEducationHistory $employeeEducationHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeEducationHistory  $employeeEducationHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeEducationHistory $employeeEducationHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeEducationHistory  $employeeEducationHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = EmployeeEducationHistory::find($request['id']);
        if (!$model) {
            $model = new EmployeeEducationHistory();
            $model->created_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->university_id = $request['university_id'];
        $model->major_id = $request['major_id'];
        $model->study_type_id = $request['study_type_id'];
        $model->study_degree_id = $request['study_degree_id'];
        $model->academic_title = $request['academic_title'];
        $model->academic_degree = $request['academic_degree'];
        // $model->university_address = $request['university_address'];
        // $model->begin_date = $request['begin_date'];
        $model->end_date = $request['end_date'];
        $model->updated_by = $request['updated_by'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeEducationHistory  $employeeEducationHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        EmployeeEducationHistory::find($id)->delete();
    }
}

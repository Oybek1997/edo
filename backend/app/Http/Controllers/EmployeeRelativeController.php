<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeRelative;
use App\Http\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EmployeeRelativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return EmployeeRelative::with('familyRelative')->get();
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
    public function show(EmployeeRelative $employeeRelative)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeRelative  $employeeRelative
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Models\EmployeeRelative  $employeeRelative
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeRelative $employeeRelative)
    {
        //
        $model = EmployeeRelative::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeRelative();
        }
        $model->employee_id = $request['employee_id'];
        $model->family_relative_id = $request['family_relative_id'];
        $model->last_name = $request['last_name'];
        $model->first_name = $request['first_name'];
        $model->middle_name = $request['middle_name'];
        $model->born_date = $request['born_date'];
        $model->born_place = $request['born_place'];
        $model->work_place = $request['work_place'];
        $model->living_place = $request['living_place'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeRelative $employeeRelative, $id)
    {
        //
        $model = EmployeeRelative::find($id);
        $model->delete();
        return 'Successfully deleted!';
    }
}

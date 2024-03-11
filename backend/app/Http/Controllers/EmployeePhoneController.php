<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeePhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmployeePhone::all();
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
     * @param  \App\Http\Models\EmployeePhone  $employeePhone
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeePhone $employeePhone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeePhone  $employeePhone
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeePhone $employeePhone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EmployeePhone  $employeePhone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeePhone $employeePhone)
    {
        //
        $model = EmployeePhone::find($request->input('id'));
        if (!$model) {
            $model = new EmployeePhone();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->phone_type = $request['phone_type'];
        $model->phone_number = $request['phone_number'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EmployeePhone  $employeePhone
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeePhone $employeePhone, $id)
    {
        //
        $model = EmployeePhone::find($id);
        $model->delete();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Models\Employee;
use App\Http\Models\EmployeeAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Models\EmployeeAddress  $employeeAddress
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeAddress $employeeAddress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeAddress  $employeeAddress
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeAddress $employeeAddress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EmployeeAddress  $employeeAddress
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, Employee $Employee)
	{
		//
		$model = EmployeeAddress::find($request->input('id'));
		if (!$model) {
			$model = new EmployeeAddress();
			$model->created_by= Auth::id();
		} else {
			$model->updated_by = Auth::id();
		}
		$model->employee_id =$request['employee_id'];
		$model->address_type_id = $request['address_type_id'];
		$model->country_id = $request['country_id'];
		$model->region_id = $request['region_id'];
		$model->district_id = $request['district_id'];
		$model->street_address = $request['street_address'];
		$model->home_address = $request['home_address'];
		$model->description = $request['description'];


		$model->save();
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EmployeeAddress  $employeeAddress
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeAddress $employeeAddress)
    {
        //
    }
}

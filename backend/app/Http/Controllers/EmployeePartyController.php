<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePartyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmployeeParty::all();
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
     * @param  \App\Http\Models\EmployeeParty  $EmployeeParty
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeParty $EmployeeParty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeParty $EmployeeParty
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeParty $EmployeeParty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EmployeeParty $EmployeeParty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeParty $EmployeeParty)
    {
        //
        $model = EmployeeParty::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeParty();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->hr_party_id = $request['hr_party_id'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EmployeeParty $EmployeeParty
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeParty $employeeParty, $id)
    {
        //
        $model = EmployeeParty::find($id);
        $model->delete();
    }
}

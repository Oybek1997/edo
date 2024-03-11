<?php

namespace App\Http\Controllers;

use App\Http\Models\ReservedEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservedEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return StaffCritical::with('reserves')
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
     * @param  \App\ReservedEmployee  $ReservedEmployee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {   
        $model = ReservedEmployee::where('critical_staff_id', $request['critical_staff_id'])->first();
        $model = new ReservedEmployee();
        $model->employee_id = $request['employee_id'];
        $model->critical_staff_id = $request['critical_staff_id'];
        $model->save();
        return 'Saved successfully!';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ReservedEmployee::find($id)->delete();
    }
}

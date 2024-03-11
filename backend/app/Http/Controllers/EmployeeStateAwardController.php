<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeStateAward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeStateAwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmployeeStateAward::all();
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
     * @param  \App\Http\Models\EmployeeStateAward $EmployeeStateAward
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeStateAward $EmployeeStateAward)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeStateAward $EmployeeStateAward
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeStateAward $EmployeeStateAward)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EmployeeStateAward $EmployeeStateAward
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeStateAward $EmployeeStateAward)
    {
        //
        $model = EmployeeStateAward::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeStateAward();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->hr_state_award_id = $request['hr_state_award_id'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EmployeeStateAward $EmployeeStateAward
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeStateAward $EmployeeStateAward, $id)
    {
        //
        $model = EmployeeStateAward::find($id);
        $model->delete();
    }
}

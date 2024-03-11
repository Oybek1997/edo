<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeMilitaryRank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeMilitaryRankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmployeeMilitaryRank::all();
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
     * @param  \App\Http\Models\EmployeeMilitaryRank $EmployeeMilitaryRank
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeMilitaryRank $EmployeeMilitaryRank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeMilitaryRank $EmployeeMilitaryRank
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeMilitaryRank $EmployeeMilitaryRank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EmployeeMilitaryRank $EmployeeMilitaryRank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeMilitaryRank $EmployeeMilitaryRank)
    {
        //
        $model = EmployeeMilitaryRank::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeMilitaryRank();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->hr_military_rank_id = $request['hr_military_rank_id'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EmployeeMilitaryRank $EmployeeMilitaryRank
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeMilitaryRank $EmployeeMilitaryRank, $id)
    {
        //
        $model = EmployeeMilitaryRank::find($id);
        $model->delete();
    }
}

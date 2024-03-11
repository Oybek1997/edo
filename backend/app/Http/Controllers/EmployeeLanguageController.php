<?php

namespace App\Http\Controllers;

use App\Http\Models\EmployeeLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeLanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmployeeLanguage::all();
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
     * @param  \App\Http\Models\EmployeeLanguage  $employeeLanguage
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeLanguage $employeeLanguage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\EmployeeLanguage  $employeeLanguage
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeLanguage  $employeeLanguage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\EmployeeLanguage  $employeeLanguage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeLanguage  $employeeLanguage)
    {
        //
        $model = EmployeeLanguage::find($request->input('id'));
        if (!$model) {
            $model = new EmployeeLanguage();
	        $model->created_by= Auth::id();
        } else {
	        $model->updated_by = Auth::id();
        }
        $model->employee_id = $request['employee_id'];
        $model->hr_language_id = $request['hr_language_id'];
        $model->level = $request['level'];
        $model->description = $request['description'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\EmployeeLanguage  $employeeLanguage
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeLanguage  $employeeLanguage, $id)
    {
        //
        $model = EmployeeLanguage::find($id);
        $model->delete();
    }
}

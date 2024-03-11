<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\CdptDepartmentType;
use App\Http\Models\Employee;
use App\Http\Models\CareerDevelopmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CareerDevelopmentPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $language = $request->input('language');
        $filter = $request->input('filter');
        $employee_id = $filter['lastname_uz_latin'];
        // $departmentType = $filter['cdpt_department_type_id'];
        $careerDevelopmentPlan = CareerDevelopmentPlan::with('employee')
            ->with('departmentType');
        
        if ($employee_id) {
            $careerDevelopmentPlan->whereIn('employee_id', Employee::where('lastname_uz_latin', 'like', '%'.$employee_id.'%')
                    ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
                    ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                    ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
        }
        if (isset($filter['cdpt_department_type_id'])) {
            $careerDevelopmentPlan->where('cdpt_department_type_id', $filter['cdpt_department_type_id']);
        }
        if (isset($filter['position'])) {
            $careerDevelopmentPlan->where('position', 'like', '%' . $filter['position'] . '%');
        }
        if (isset($filter['year'])) {
            $careerDevelopmentPlan->where('year', 'like', '%' . $filter['year'] . '%');
        }
        if (isset($filter['goal'])) {
            $careerDevelopmentPlan->where('goal', 'like', '%' . $filter['goal'] . '%');
        }
        

        return $careerDevelopmentPlan->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
    public function show(CareerDevelopmentPlan $CareerDevelopmentPlan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CareerDevelopmentPlan $CareerDevelopmentPlan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CareerDevelopmentPlan $CareerDevelopmentPlan)
    {
        $model = CareerDevelopmentPlan::find($request->input('id'));
        if (!$model) {
            $model = new CareerDevelopmentPlan();
            $model->created_by= Auth::id();
        } else {
            $model->updated_by = Auth::id();
        }
        $model->cdpt_department_type_id = $request['cdpt_department_type_id'];
        $model->employee_id = $request['employee_id'];
        $model->position = $request['position'];
        $model->year = $request['year'];
        $model->goal = $request['goal'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CareerDevelopmentPlan $CareerDevelopmentPlan, $id)
    {
        $model = CareerDevelopmentPlan::find($id);
        $model->delete();
    }
}

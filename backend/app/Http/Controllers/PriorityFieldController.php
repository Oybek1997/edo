<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Models\CdptDepartmentType;
use App\Http\Models\PriorityField;
use App\Http\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriorityFieldController extends Controller
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
        $priorityField = PriorityField::with('employee')
            ->with('departmentType');

        if (isset($filter['sequence'])) {
            $priorityField->where('sequence', 'like', '%' . $filter['sequence'] . '%');
        }
        if (isset($filter['cdpt_department_type_id'])) {
            $priorityField->where('cdpt_department_type_id', 'like', '%' . $filter['cdpt_department_type_id'] . '%');
        }
        // if (isset($filter['cdpt_department_type_id'])) {
        //     $departmentType->where('departmentType.cdpt_department_type_id', $filter['cdpt_department_type_id']);
        // }
        if ($employee_id) {
            $priorityField->whereIn('employee_id', Employee::where('lastname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_latin', 'like', '%'.$employee_id.'%')
                ->orWhere('lastname_uz_cyril', 'like', '%'.$employee_id.'%')
                ->orWhere('firstname_uz_cyril', 'like', '%'.$employee_id.'%')->pluck('id')->toArray());
        }

        return $priorityField->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
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
    public function show(PriorityField $PriorityField)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PriorityField $PriorityField)
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
    public function update(Request $request, PriorityField $PriorityField)
    {
        $model = PriorityField::find($request->input('id'));
        if (!$model) {
            $model = new PriorityField();
            $model->created_by= Auth::id();
        }
        // else {
        //     $model->updated_by = Auth::id();
        // }
        $model->cdpt_department_type_id = $request['cdpt_department_type_id'];
        $model->employee_id = $request['employee_id'];
        $model->sequence = $request['sequence'];
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PriorityField $PriorityField, $id)
    {
        $model = PriorityField::find($id);
        $model->delete();
    }
}

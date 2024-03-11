<?php

namespace App\Http\Controllers;

use App\Http\Models\StaffCritical;
use App\Http\Models\ReservedEmployee;
use App\Http\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffCriticalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return StaffCritical::with('employee')
        ->with('staff.position')
        ->with('staff.department')
        ->with('reserves.employee')
        ->get();
    }

    public function getRef($locale)
    {
        // return Employee::select()->get();
        return Employee::select(
            'id',
            'tabel',
            $locale == 'uz_latin' ? 'firstname_uz_latin' : 'firstname_uz_cyril',
            $locale == 'uz_latin' ? 'middlename_uz_latin' : 'middlename_uz_cyril',
            $locale == 'uz_latin' ? 'lastname_uz_latin' : 'lastname_uz_cyril'
        )->where('is_active', 1)->get();
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
     * @param  \App\StaffCritical  $StaffCritical
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = StaffCritical::where('staff_id', $id)
                ->where('end_date', null)
                ->first();
        return $model;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StaffCritical  $StaffCritical
     * @return \Illuminate\Http\Response
     */
    public function edit(StaffCritical $StaffCritical)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StaffCritical  $StaffCritical
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $form = $request->input('form');
        $model = StaffCritical::find($form['id']);
        if (!$model) {
            $model = new StaffCritical();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = $form['updated_by'];
        }
        $model->employee_id = $form['employee_id'];
        $model->staff_id = $form['staff_id'];
        $model->begin_date = $form['begin_date'];
        $model->end_date = isset($form['end_date']) ? $form['end_date'] : null;
        $model->description = $form['description'];
        $model->save();
        return 'Saved successfully!';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StaffCritical $StaffCritical
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        StaffCritical::find($id)->delete();
    }
    public function getExcel(Request $request)
    {
        $page = $request->input('page');
        $locale = $request->input('locale') == 'ru' ? 'uz_cyril' : $request->input('locale');
        $_locale = $request->input('locale');
        $perPage = $request->input('perPage');
        // $employees = Employee::with(['employeeStaffWithInactive' => function ($query) {
        //     $query->with('tariffScale')
        //         ->with(['staff' => function ($staffQuery) {
        //             $staffQuery->with('position')->with('department');
        //         }]);
        // }])
        //     ->select('employees.*')
        //     ->leftJoin('employee_staff', 'employee_staff.employee_id', '=', 'employees.id')
        //     ->leftJoin('staff', 'staff.id', '=', 'employee_staff.staff_id')
        //     ->leftJoin('departments', 'departments.id', '=', 'staff.department_id')
        //     ->orderByRaw('departments.department_code ASC')
        //     ->distinct('employees.tabel')
        //     ->paginate($perPage, ['*'], 'page name', $page);
        // $excel = [];
        // $department_code = '';
        // $category = '';
        // $department = '';
        // $position = '';
        // $first_work_date = '';
        // $leave_date = '';
        // foreach ($employees as $key => $value) {
        //     foreach ($value->employeeStaffWithInactive as $key_codes => $value_codes) {
        //         if ($value_codes->is_main_staff == 1)
        //         {
        //             $department_code = $value_codes->staff && $value_codes->staff->department ? $value_codes->staff->department->department_code : '';
        //             $department = $value_codes->staff && $value_codes->staff->department ? $value_codes->staff->department['name_' . $_locale] : '';
        //             $category = $value_codes->tariffScale ? $value_codes->tariffScale->category : '';
        //             $position = $value_codes->staff && $value_codes->staff->position ? $value_codes->staff->position['name_' . $_locale] : "";
        //             $first_work_date = $value_codes->first_work_date;
        //             $leave_date = $value_codes->leave_date ? $value_codes->leave_date : '';
        //         }
        //     }
        //     array_push($excel, (object)[
        //         "№" => $key + 1 + $page * $perPage - $perPage,
        //         "Код подразделения" => $department_code,
        //         "Табелный номер" => '*' . $value->tabel,
        //         "Сотрудник" => $value['firstname_' . $locale] . ' ' . $value['lastname_' . $locale] . ' ' . $value['middlename_' . $locale],
        //         "Категория" => $category,
        //         "Подразделения" => $department,
        //         "Должность" => $position,
        //         "Дата приема на работу" => $first_work_date,
        //         "Дата увольнения" => $leave_date,
        //     ]);
        // }

        $staffCriticals = StaffCritical::with('employee')
        ->with('staff.position')
        ->with('staff.department')
        ->get();
        $excel = [];
        $department = '';
        $position = '';
        $begin_date = '';
        $end_date = '';
        $description = '';
        foreach ($staffCriticals as $key => $value) {
            // return $value;
            $department = $value->staff->department ? $value->staff->department['name_' . $_locale] : '';
            $position = $value->staff->position ? $value->staff->position['name_' . $_locale] : "";
            $begin_date = $value->begin_date ? $value->begin_date : '';
            $end_date = $value->end_date ? $value->end_date : '';
            $description = $value->description ? $value->description : '';
            // return $position;
            // foreach ($value->staff as $key_codes => $value_codes) {
            //     if ($value_codes->is_main_staff == 1)
            //     {
            //         $department = $value_codes->staff && $value_codes->staff->department ? $value_codes->staff->department['name_' . $_locale] : '';
            //         $position = $value_codes->staff && $value_codes->staff->position ? $value_codes->staff->position['name_' . $_locale] : "";
            //     }
            // }
            array_push($excel, (object)[
                "№" => $key + 1 + $page * $perPage - $perPage,
                "Сотрудник" => $value->employee['firstname_' . $locale] . ' ' . $value->employee['lastname_' . $locale] . ' ' . $value->employee['middlename_' . $locale],
                "Подразделения" => $department,
                "Должность" => $position,
                "Дата начала" => $begin_date,
                "Дата окончания" => $end_date,
                "Описание" => $description,
            ]);
        }
        return $excel;
    }
}

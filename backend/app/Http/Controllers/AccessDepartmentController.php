<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\AccessDepartment;
use App\Http\Models\Department;
use App\Http\Models\AccessType;
use App\Http\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class AccessDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $accessDepartments = AccessDepartment::with('employee')
            ->with('department')
            ->with('accessType')
            // ->get();
            // return $accessDepartments;
            ->leftJoin('departments', 'departments.id', '=', 'access_departments.department_id')
            ->leftJoin('employees', 'employees.id', '=', 'access_departments.employee_id')
            ->leftJoin('access_types', 'access_types.id', '=', 'access_departments.access_type_id')
            ->orderByRaw('departments.department_code ASC');
            // ->whereNull('deleted_at');

        // if (isset($filter['department_info'])) {
        //     $accessDepartments->where(function (Builder $query) use ($filter) {
        //         return $query
        //             ->where(DB::raw("concat(departments.department_code,' ', departments.name_uz_latin)"), 'like', "%" . $filter['department_info'] . "%")
        //             ->orWhere(DB::raw("concat(departments.department_code,' ', departments.name_uz_cyril)"), 'like', "%" . $filter['department_info'] . "%")
        //             ->orWhere(DB::raw("concat(departments.department_code,' ', departments.name_ru)"), 'like', "%" . $filter['department_info'] . "%");
        //     });
        // }
        if (isset($filter['department_code'])) {
            $accessDepartments->where(function (Builder $query) use ($filter) {
                return $query->where('departments.department_code', 'like', $filter['department_code'] . "%");
            });
        }
        if (isset($filter['department_name'])) {
            $accessDepartments->where(function (Builder $query) use ($filter) {
                return $query->where('departments.name_ru', 'like', "%" . $filter['department_name'] . "%")
                    ->orWhere('departments.name_uz_latin', 'like', "%" . $filter['department_name'] . "%")
                    ->orWhere('departments.name_uz_cyril', 'like', "%" . $filter['department_name'] . "%");
            });
        }
        if (isset($filter['info'])) {
            $accessDepartments->where(function (Builder $query) use ($filter) {
                return $query
                    ->where(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'like', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'like', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'like', "%" . $filter['info'] . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'like', "%" . $filter['info'] . "%");
            });
        }
        if (isset($filter['tabel'])) {
            $accessDepartments->where('tabel', 'like', '%' . $filter['tabel'] . '%');
        }
        if (isset($filter['access_type'])) {
            $accessDepartments->where(function (Builder $query) use ($filter) {
                return $query->where('access_types.name_ru', 'like', "%" . $filter['access_type_name'] . "%")
                    ->orWhere('access_types.name_uz_latin', 'like', "%" . $filter['access_type_name'] . "%")
                    ->orWhere('access_types.name_uz_cyril', 'like', "%" . $filter['access_type_name'] . "%");
            });
        }
        return  json_encode([$accessDepartments->select('access_departments.*')->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page)]);
        // return  json_encode([$accessDepartments->select('access_departments.*')->distinct()->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page)]);
    }

    public function getRef($locale)
    {
        // $language = $request->input('language');
        return [
            'departments' => Department::select('id', 'department_code', 'name_' . $locale)->get(),
            'access_types' => AccessType::select('id', 'name_' . $locale)->get(),
            'employees' => Employee::select(
                'id',
                'tabel',
                $locale == 'uz_latin' ? 'firstname_uz_latin' : 'firstname_uz_cyril',
                $locale == 'uz_latin' ? 'middlename_uz_latin' : 'middlename_uz_cyril',
                $locale == 'uz_latin' ? 'lastname_uz_latin' : 'lastname_uz_cyril'
            )->get(),
        ];
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // $model = AccessDepartment::find($request->input('id'));
        // if ($model) {
        //     // $model->delete();
        //     // $model = new AccessDepartment();
        //     // $access_type = $model->access_type_id;
        //     $model->access_type_id = $access_type;
        // } elseif (!$model) {
        //     $model = new AccessDepartment();
        //     $model->access_type_id = $request['access_type_id'];
        // }
        // // $model->department_id = $request['department_id'];
        $department = $request->department_id;
        $employee = $request['employee_id'];
        foreach ($department as $value) {
            $model = AccessDepartment::where('employee_id', $employee)->where('department_id', $value)->first();
            if(!$model){
                $model = new AccessDepartment();
                $model->employee_id = $request['employee_id'];
                $model->department_id = $value;
                $model->access_type_id = 1;
                $model->save();
            }
        }
        // $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = AccessDepartment::findOrfail($id);
        $model->delete();
    }
}

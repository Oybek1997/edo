<?php

namespace App\Http\Models;

use Adldap\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';

    protected $fillable = [
        'company_id',
        'parent_id',
        'department_type_id',
        'department_code',
        'name_uz_latin',
        'name_uz_cyril',
        'name_ru',
    ];


    public function functionalDepartment()
    {
        return $this->hasOne('App\Http\Models\FunctionalDepartment', 'id', 'functional_department_id');
    }
    public function company()
    {
        return $this->hasOne('App\Http\Models\Company', 'id', 'company_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'department2_id', 'id');
    }

    public function access()
    {
        return $this->hasMany('App\Http\Models\AccessDepartment', 'department_id', 'id');
    }

    public function users()
    {
        return $this->hasMany('App\User', 'department_id', 'id');
    }

    public function departmentType()
    {
        return $this->hasOne('App\Http\Models\DepartmentType', 'id', 'department_type_id');
    }

    public function managerStaff()
    {
        return $this->hasOne('App\Http\Models\Staff', 'id', 'manager_staff_id')->where('is_active', 1);
    }

    public function parent()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'parent_id');
    }
    public function functionalParent()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'functional_parent_id');
    }

    public function branch()
    {
        return $this->hasOne('App\Http\Models\Branch', 'id', 'branch_id');
    }

    public function children()
    {
        $user = Auth::id();
        if ($user == 518) {
            return $this->hasMany('App\Http\Models\Department', 'company_id', 'id');
        } else {
            return $this->hasMany('App\Http\Models\Department', 'parent_id', 'id');
        }
    }

    public function employeeStaffs()
    {
        return $this->hasMany('App\Http\Models\EmployeeStaff', 'staff_id', 'staff_id');
    }

    public function staff()
    {
        return $this->hasMany('App\Http\Models\Staff', 'department_id', 'id')->where('is_active', 1);
    }

    public function employeeStaff()
    {
        return $this->hasOne('App\Http\Models\EmployeeStaff', 'staff_id', 'manager_staff_id')->where('employee_staff.is_active', 1);
    }

    public function employeeMainStaff()
    {
        return $this->hasOne('App\Http\Models\EmployeeStaff', 'staff_id', 'id')->where('is_main_staff', 1)->where('is_active', 1);
    }



    public function jointCompany()
    {
        return $this->hasOne('App\Http\Models\Company', 'dep_id', 'id');
    }

    public static function tree($id)
    {
        $departments = Department::with('departmentType')
            ->with(['employeeStaff' => function ($query) {
                $query->where('is_active', 1)->with('employee');
            }])
            ->where('parent_id', $id)
            ->where('is_active', 1)
            ->orderBy('department_code', 'asc')
            ->where('department_type_id', '<>', 13)
            ->get();
        $arr = [];
        foreach ($departments as $key => $value) {
            $arr[$key] = $value;
            try {
                $arr[$key]['children'] = Department::tree($value->id);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return $arr;
    }

    public static function orgChartNew3($id, $parent_id, $step)
    {
        $data = [];
        $departments = Department::select('id', 'parent_id', 'functional_parent_id', 'name_uz_latin', 'manager_staff_id');
        if ($parent_id) {
            $departments->where('id', $parent_id);
        } else {
            $departments->where('functional_parent_id', $id);
        }
        $departments = $departments->get();

        foreach ($departments as $key => $value) {
            $data[$key]['id'] = $value->id;
            $data[$key]['name'] = $value->name_uz_latin;
            $data[$key]['root'] = false;
            $data[$key]['expand'] = false;

            if ($step == 1) {
                $data[$key]['children'] = Department::orgChartNew3($value->id, null, 2);
            } else {
                $data[$key]['children'] = [];
            }

            if ($staff = $value->managerStaff) {
                $data[$key]['position'] = $staff->position->name_uz_latin;
                try {
                    $employee = $value->managerStaff->managerEmployee[0];
                    $data[$key]['manager'] = $employee ? $employee->fi() : '';
                    if ($employee && (file_exists(storage_path('app/avatars/' . $employee->tabel . '.JPG')) || file_exists(storage_path('app/avatars/' . $employee->tabel . '.jpg')))) {
                        $data[$key]['tabel'] = $employee->tabel;
                    } else {
                        $data[$key]['tabel'] = 'avatar';
                    }
                } catch (\Throwable $th) {
                    $data[$key]['manager'] = '';
                    $data[$key]['tabel'] = '';
                }
            }
        }
        return $data;
    }

    public static function orgChartNew2($id, $parent_id, $step)
    {
        $data = [];
        $departments = Department::select('id', 'parent_id', 'name_uz_latin', 'manager_staff_id', 'head_count', 'rate_count', 'rate_count_bp');
        if ($parent_id) {
            $departments->where('id', $parent_id);
        } else {
            $departments->where('parent_id', $id);
        }
        $departments = $departments->get();

        foreach ($departments as $key => $value) {
            // $st=$value->staff;
            // $bp = 0;
            // $rc = 0;
            // foreach ($st as $k => $v) {
            //     $bp += $v->rate_count_bp;
            //     $rc += $v->rate_count;
            // }
            $data[$key]['id'] = $value->id;
            $data[$key]['parent_id'] = $value->parent_id;
            $data[$key]['head_count'] = $value->head_count;
            $data[$key]['rate_bp'] = $value->rate_count_bp;
            $data[$key]['rate_count'] = $value->rate_count;
            $data[$key]['name'] = $value->name_uz_latin;
            $data[$key]['root'] = false;
            $data[$key]['expand'] = false;

            if ($step == 1) {
                $data[$key]['children'] = Department::orgChartNew2($value->id, null, 2);
            } else {
                $data[$key]['children'] = [];
            }

            if ($staff = $value->managerStaff) {
                $data[$key]['position'] = $staff->position->name_uz_latin;
                $data[$key]['range'] = $staff->range ? $staff->range->code: '';
                try {
                    $employee = $value->managerStaff->managerEmployee[0];
                    $data[$key]['manager'] = $employee ? $employee->fi() : '';
                    $data[$key]['tariff'] = $employee ? $employee->tariffScale->category : '';
                    if ($employee && (file_exists(storage_path('app/avatars/' . $employee->tabel . '.JPG')) || file_exists(storage_path('app/avatars/' . $employee->tabel . '.jpg')))) {
                        $data[$key]['tabel'] = $employee->tabel;
                    } else {
                        $data[$key]['tabel'] = 'avatar';
                    }
                } catch (\Throwable $th) {
                    $data[$key]['manager'] = '';
                    $data[$key]['tabel'] = '';
                }
            }
        }
        return $data;
    }

    public static function orgChartNew($id, $step)
    {
        $data = [];
        $departments = Department::select('id', 'parent_id', 'name_uz_latin', 'manager_staff_id', 'head_count', 'rate_count', 'rate_count_bp');
        if ($id == null) {
            $departments->where('id', 1);
        } else {
            $departments->where('parent_id', $id);
        }

        $departments = $departments->get();

        foreach ($departments as $key => $value) {
            $data[$key]['id'] = $value->id;
            $data[$key]['name'] = $value->name_uz_latin;
            $data[$key]['parent_id'] = $value->parent_id;
            $data[$key]['head_count'] = $value->head_count;
            $data[$key]['rate_bp'] = $value->rate_count_bp;
            $data[$key]['rate_count'] = $value->rate_count;
            $data[$key]['root'] = false;
            $data[$key]['expand'] = false;

            // if ($step == 1) {
            //     $data[$key]['children'] = Department::orgChartNew($value->id, 2);
            // } else {
            //     $data[$key]['children'] = [];
            // }

            if ($staff = $value->managerStaff) {
                $data[$key]['position'] = $staff->position->name_uz_latin;
                $data[$key]['range'] = $staff->range ? $staff->range->code: '';
                try {
                    $employee = $value->managerStaff->managerEmployee[0];
                    $data[$key]['manager'] = $employee ? $employee->fi() : '';
                    $data[$key]['tariff'] = $employee ? $employee->tariffScale->category : '';
                    if ($employee && (file_exists(storage_path('app/avatars/' . $employee->tabel . '.JPG')) || file_exists(storage_path('app/avatars/' . $employee->tabel . '.jpg')))) {
                        $data[$key]['tabel'] = $employee->tabel;
                    } else {
                        $data[$key]['tabel'] = 'avatar';
                    }
                } catch (\Throwable $th) {
                    $data[$key]['manager'] = '';
                    $data[$key]['tabel'] = '';
                }
            }
        }
        return $data;
    }

    public static function orgChart($id, $step)
    {
        $departments = Department::with('departmentType')
            ->with(['employeeStaff' => function ($query) {
                $query->where('is_active', 1)->with('employee')
                    ->with(['employee' => function ($q) {
                        $q->select('id', 'tabel', DB::raw("concat(firstname_uz_latin,' ',lastname_uz_latin) as manager", 'tabel'));
                    }]);
            }])
            ->select('id', 'name_ru as label', 'department_type_id', 'department_code as code', 'manager_staff_id')
            ->where('parent_id', $id)
            ->orderBy('department_code', 'asc')
            ->where('department_type_id', '<>', 13)
            ->get();
        $arr = [];
        foreach ($departments as $key => $value) {

            $dep_id = $value->id;
            $employees = Employee::whereHas('staff', function ($q) use ($dep_id) {
                $q->where('department_id', $dep_id);
            })
                ->with(['staff' => function ($q) use ($dep_id) {
                    $q->select('staff.id', 'position_id')
                        ->where('department_id', $dep_id)
                        ->with('position:name_uz_latin as name,id');
                }])
                ->select('id', 'tabel', DB::raw("concat(firstname_uz_latin,' ',lastname_uz_latin) as fullname", 'tabel'))
                ->get()
                ->toArray();

            $arr[$key]['id'] = $value->id;
            $arr[$key]['employees'] = $employees;
            $arr[$key]['code'] = $value->code;
            $arr[$key]['label'] = $value->label;
            $arr[$key]['manager'] = '';
            $arr[$key]['tabel'] = null;
            $arr[$key]['root'] = true;
            $arr[$key]['show_employee'] = false;
            if ($value->employeeStaff) {
                $arr[$key]['manager'] = $value->employeeStaff->employee->manager;
                $arr[$key]['tabel'] = $value->employeeStaff->employee->tabel;
            }
            $arr[$key]['department_type'] = $value->department_type ? $value->department_type->name_ru : '';
            $arr[$key]['expand'] = false;
            if ($step == 1) {
                $arr[$key]['children'] = Department::orgChart($value->id, 2);
            } else $arr[$key]['children'] = [];
        }
        return $arr;
    }

    public static function tree1($id)
    {
        $departments = Department::with('departmentType')
            ->with(['employeeStaff' => function ($query) {
                $query->where('is_active', 1)->with('employee');
            }])
            ->where('parent_id', $id)
            ->orderBy('department_code', 'asc')
            ->get();
        $arr = [];
        foreach ($departments as $key => $value) {
            // $arr[$key] = $value;
            $arr[$key]['id'] = $value->id;
            $arr[$key]['name'] = $value->name_ru;
            $arr[$key]['title'] = $value->name_ru;
            $arr[$key]['children'] = Department::tree1($value->id);
        }
        return $arr;
    }
    public function kpiObjektdep()
    {
        return $this->hasMany('App\Http\Models\KpiObject', 'dep_id', 'id');
    }
}

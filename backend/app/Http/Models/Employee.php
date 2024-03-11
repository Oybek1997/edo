<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $connection = 'pgsql';

    protected $appends = ['fio', 'full_fio'];

    public static function employeeDepartments($emp_id, $locale)
    {

        $manager_staff = [];
        // $oldDepartment = '';

        $manager_staff = Employee::with('staff')
            ->with('staff.position')->with('staff.department')->where('id', $emp_id)->first();

        $from_department = $manager_staff ? $manager_staff->staff[0]->department['name_' . $locale] : '';
        $from_manager = $manager_staff['lastname_' . $locale] . ' ' . $manager_staff['firstname_' . $locale] . ' ' . $manager_staff['middlename_' . $locale];;
        $from_position = '';
        if ($manager_staff && $manager_staff->staff && $manager_staff->staff[0]->position) {
            $from_position = $manager_staff->staff[0]->position['name_' . $locale];
        }
        return ['from_department' => $from_department, 'from_manager' => $from_manager, 'from_position' => $from_position,];
    }
    public static function parentDepartments($tabel)
    {
        $employee = Employee::select('staff.department_id')
            ->join('employee_staff', 'employee_staff.employee_id', '=', 'employees.id')
            ->join('staff', 'employee_staff.staff_id', '=', 'staff.id')
            ->where('employees.tabel', $tabel)
            ->where('employee_staff.is_active', 1)
            ->orderBy('employee_staff.is_main_staff', 'desc')
            ->first();
        if (!$employee) {
            return false;
        }

        $manager_staff = [];
        $department = Department::find($employee->department_id);
        $oldDepartment = '';

        if ($department->id == 749) {
            // dd($department->departmentType->sequence, $department->parent->departmentType->sequence);
        }
        if ($department->departmentType->sequence < 3 /*|| $department->parent->departmentType->sequence == 2*/) {
            return [
                'manager_staff' => Department::with('departmentType')->with('managerStaff')->with('managerStaff.employees')->with('managerStaff.position')->where('id', $department->id)->get(),
                'main_department' => Department::with('managerStaff.position')->with('managerStaff.employeeOldStaff.employee')->where('id', $department->id)->first()
            ];
        }

        do {
            $manager_staff[] = Department::with('departmentType')->with('managerStaff')->with('managerStaff.employees')->with('managerStaff.position')->where('id', $department->id)->first();
            if ($department->departmentType->sequence > 2) {
                $oldDepartment = Department::with('departmentType')->with('managerStaff.position')->with('managerStaff.employeeOldStaff.employee')->where('id', $department->id)->first();
            }
            $department = $department->parent;
        } while ($department && $department->departmentType->sequence > 1);


        //   Agar funksional guruhga bo'ysunuvchi bo'lim yoki bo'linma bo'lsa funksiyanal guruh chiqishi uchun
        if ($oldDepartment->departmentType->sequence == 5) {
            $manager_staff = [];
            $department = Department::find($employee->department_id);
            $oldDepartment = '';

            if ($department->departmentType->sequence < 3) {
                return [
                    'manager_staff' => Department::with('departmentType')->with('managerStaff')->with('managerStaff.employees')->with('managerStaff.position')->where('id', $department->id)->get(),
                    'main_department' => Department::with('managerStaff.position')->with('managerStaff.employeeOldStaff.employee')->where('id', $department->id)->first()
                ];
            }

            do {
                $manager_staff[] = Department::with('departmentType')->with('managerStaff')->with('managerStaff.employees')->with('managerStaff.position')->where('id', $department->id)->first();
                if ($department->departmentType->sequence > 1) {
                    $oldDepartment = Department::with('departmentType')->with('managerStaff.position')->with('managerStaff.employeeOldStaff.employee')->where('id', $department->id)->first();
                }
                $department = $department->parent;
            } while ($department && $department->departmentType->sequence > 1);
            // dd($oldDepartment->departmentType->sequence);
        }

        return ['manager_staff' => $manager_staff, 'main_department' => $oldDepartment];
    }

    public function getFullname($locale)
    {
        $firstname = ($locale == 'uz_latin') ? 'firstname_uz_latin' : 'firstname_uz_cyril';
        $lastname = ($locale == 'uz_latin') ? 'lastname_uz_latin' : 'lastname_uz_cyril';
        $middlename = ($locale == 'uz_latin') ? 'middlename_uz_latin' : 'middlename_uz_cyril';
        return $this->$firstname . ' ' . $this->$lastname . ' ' . $this->$middlename;
    }

    public function getShortname($locale)
    {
        $firstname = ($locale == 'uz_latin') ? 'firstname_uz_latin' : 'firstname_uz_cyril';
        $lastname = ($locale == 'uz_latin') ? 'lastname_uz_latin' : 'lastname_uz_cyril';
        $middlename = ($locale == 'uz_latin') ? 'middlename_uz_latin' : 'middlename_uz_cyril';
        $count = $locale == 'uz_latin' ? 1 : 2;
        return substr($this->$firstname, 0, $count) . '.' . substr($this->$middlename, 0, $count) . '. ' . $this->$lastname;
    }

    public function getFioAttribute()
    {
        $text = '';
        if ($this->firstname_uz_latin)
            $text .= mb_substr($this->firstname_uz_latin, 0, 1) . '.';
        if ($this->middlename_uz_latin)
            $text .= mb_substr($this->middlename_uz_latin, 0, 1) . '.';
        if ($this->lastname_uz_latin)
            $text .= $this->lastname_uz_latin;
        return $text;
    }

    public function getFullFioAttribute()
    {
        $text = '';
        if ($this->firstname_uz_latin)
            $text .= $this->firstname_uz_latin . ' ';
        if ($this->lastname_uz_latin)
            $text .= $this->lastname_uz_latin . ' ';
        if ($this->middlename_uz_latin)
            $text .= $this->middlename_uz_latin;
        return $text;
    }

    public function fi()
    {
        return $this->firstname_uz_latin . ' ' . $this->lastname_uz_latin;
    }

    public static function getStaffHistory($employee_id, $locale)
    {
        $es = EmployeeStaff::where('employee_id', $employee_id)
            ->where('is_main_staff', 1)
            ->get();
        $staffs = [];
        foreach ($es as $key => $value) {
            $dep = $value->staff->departmentWithTrashed;
            // if ($dep != null) 
            {
                $s['department'] = $dep['name_' . $locale];
                $s['department_code'] = $dep['department_code'];
                try {
                    while ($dep->parent_id && $dep->department_type_id != 3) {

                        $dep = $dep->parent;
                    }
                } catch (\Throwable $th) {
                    if ($dep == null)
                        continue;
                    throw $th;
                }

                $s['parent'] = $dep->department_type_id == 3 ? $dep['name_' . $locale] : '';
                $s['position'] = $value->staff->position['name_' . $locale];
                $s['enterOrderDate'] = $value->enter_order_date;
                $s['leaveOrderDate'] = $value->leave_order_date;
                $s['createdAt'] = $value->created_at->toDateString();
                $s['updatedAt'] = $value->updated_at->toDateString();
                $s['id'] = $value->id;
                $staffs[] = $s;
            }
        }
        return $staffs;
    }

    public function tariffScale()
    {
        return $this->hasOne('App\Http\Models\TariffScale', 'id', 'tariff_scale_id');
    }

    /**
     * Get all of the deviceHistory for the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceHistory()
    {
        return $this->hasMany(DeviceHistory::class, 'employee_id', 'id');
    }

    /**
     * The roles that belong to the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function devices()
    {
        return $this->belongsToMany(Device::class, 'device_histories', 'employee_id', 'device_id');
    }

    public function mainStaff()
    {
        return $this->belongsToMany('App\Http\Models\Staff', 'employee_staff', 'employee_id', 'staff_id')->where('employee_staff.is_main_staff', 1)->where('employee_staff.is_active', 1);
    }

    public function additionalStaff()
    {
        return $this->belongsToMany('App\Http\Models\Staff', 'employee_staff', 'employee_id', 'staff_id')->where('employee_staff.is_main_staff', '!=', 1)->where('employee_staff.is_active', 1);
    }

    public function staff()
    {
        return $this->belongsToMany('App\Http\Models\Staff', 'employee_staff', 'employee_id', 'staff_id')->where('employee_staff.is_active', 1)->orderBy('employee_staff.is_main_staff', 'desc');
    }

    public function employeeStaff()
    {
        return $this->hasMany('App\Http\Models\EmployeeStaff', 'employee_id', 'id')->where('is_active', 1);
    }
    public function employeeStaffAll()
    {
        return $this->hasMany('App\Http\Models\EmployeeStaff', 'employee_id', 'id');
    }

    public function employeeStaffWithInactive()
    {
        return $this->hasMany('App\Http\Models\EmployeeStaff', 'employee_id', 'id')->withTrashed();
    }
    public function employeefile()
    {
        return $this->hasMany('App\Http\Models\File', 'object_id', 'id');
    }

    // public function employeefile()
    // {
    //     return $this->hasOne('App\Http\models\Employee', 'id', 'object_id');
    // }



    public function employeeCoefficients()
    {
        return $this->hasMany('App\Http\Models\EmployeeCoefficient', 'employee_id', 'id');
    }

    public function coefficients()
    {
        return $this->belongsToMany('App\Http\Models\Coefficient', 'employee_coefficients', 'employee_id', 'coefficient_id');
    }

    public function employeeAddresses()
    {
        return $this->hasMany('App\Http\Models\EmployeeAddress', 'employee_id', 'id');
    }
    public function employeePhones()
    {
        return $this->hasMany('App\Http\Models\EmployeePhone', 'employee_id', 'id');
    }
    public function employeeLanguages()
    {
        return $this->hasMany('App\Http\Models\EmployeeLanguage', 'employee_id', 'id');
    }
    public function employeeParties()
    {
        return $this->hasOne('App\Http\Models\EmployeeParty', 'employee_id', 'id');
    }
    public function employeeMilitaryRanks()
    {
        return $this->hasMany('App\Http\Models\EmployeeMilitaryRank', 'employee_id', 'id');
    }
    public function employeeStateAwards()
    {
        return $this->hasMany('App\Http\Models\EmployeeStateAward', 'employee_id', 'id');
    }
    public function employeeWorkHistories()
    {
        return $this->hasMany('App\Http\Models\EmployeeWorkHistory', 'employee_id', 'id');
    }

    public function employeeEducationHistories()
    {
        return $this->hasMany('App\Http\Models\EmployeeEducationHistory', 'employee_id', 'id');
    }
    public function company()
    {
        return $this->hasOne('App\Http\Models\Company', 'id', 'company_id');
    }

    public function country()
    {
        return $this->hasOne('App\Http\Models\Country', 'id', 'country_id');
    }

    public function nationality()
    {
        return $this->hasOne('App\Http\Models\Nationality', 'id', 'nationality_id');
    }

    public function region()
    {
        return $this->hasOne('App\Http\Models\Region', 'id', 'region_id');
    }

    public function district()
    {
        return $this->hasOne('App\Http\Models\District', 'id', 'district_id');
    }

    public function employeeOfficialDocument()
    {
        return $this->hasMany('App\Http\Models\EmployeeOfficialDocument', 'employee_id', 'id');
    }

    public function employeeRelative()
    {
        return $this->hasMany('App\Http\Models\EmployeeRelative', 'employee_id', 'id');
    }
    public function employeeCapital()
    {
        return $this->hasMany('App\Http\Models\EmployeeCapital', 'employee_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'employee_id', 'id');
    }

    public function staffCritical()
    {
        return $this->hasOne('App\Http\Models\StaffCritical', 'employee_id', 'id');
    }
    public function worktasks()
    {
        return $this->setConnection('mysql_workflow_worktask')->hasMany(WorkTaskAssignment::class, 'employee_id', 'id');
    }
}

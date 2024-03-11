<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    public $fillable = [
        'name_uz_latin',
        'name_uz_cyril',
        'name_ru',
        'branch_id',
        'department_id',
        'position_id',
        'range_id',
        'personal_type_id',
        'expence_type_id',
        'personal_count',
        'order_date',
        'order_number',
        'begin_date',
        'end_date',
    ];

    public function requirements()
    {
        return $this->belongsToMany('App\Http\Models\Requirement', 'staff_requirements', 'staff_id', 'requirement_id');
    }
    public function uzautoJobsStatus()
    {
        return $this->hasOne('App\Http\Models\Uzautojobs\SelectionStatus', 'staff_id', 'id')
        ->orderBy('id', 'DESC')
        ;
    }

    public function files()
    {
        return $this->belongsToMany('App\Http\Models\File', 'staff_files', 'staff_id', 'file_id');
    }

    public function employeeStaff()
    {
        return $this->hasMany('App\Http\Models\EmployeeStaff', 'staff_id', 'id')->where('is_active',1);
    }

    public function managerEmployee()
    {
        return $this->belongsToMany('App\Http\Models\Employee', 'employee_staff', 'staff_id', 'employee_id')->where('employee_staff.is_active', 1);
    }

    public function documentStaffs()
    {
        return $this->hasMany('App\Http\Models\DocumentStaff', 'staff_id', 'id');
    }
    public function stafMinReq()
    {
        return $this->hasMany('App\Http\Models\stafMinRequirements', 'staff_id', 'id')
            ->leftJoin('min_requirements', 'min_requirements.id', '=', 'staf_min_requirements.min_req_id')
            // ->with('minReqType')
            ;
    }
    

    public function employeeMainStaff()
    {
        return $this->hasOne('App\Http\Models\EmployeeStaff', 'staff_id', 'id')->where('is_main_staff', 1)->where('is_active', 1);
    }

    public function employeeOldStaff()
    {
        return $this->hasOne('App\Http\Models\EmployeeStaff', 'staff_id', 'id')->where('is_active', 1);
    }

    public function employees()
    {
        return $this->belongsToMany('App\Http\Models\Employee', 'employee_staff', 'staff_id', 'employee_id')->where('employee_staff.is_active', 1);
    }

    public function expenceType()
    {
        return $this->hasOne('App\Http\Models\ExpenceType', 'id', 'expence_type_id');
    }

    public function personalType()
    {
        return $this->hasOne('App\Http\Models\PersonalType', 'id', 'personal_type_id');
    }

    public function range()
    {
        return $this->hasOne('App\Http\Models\Range', 'id', 'range_id');
    }
    public function branch()
    {
        return $this->hasOne('App\Http\Models\Branch', 'id', 'branch_id');
    }

    public function position()
    {
        return $this->hasOne('App\Http\Models\Position', 'id', 'position_id');
    }

    public function department()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'department_id');
    }

    public function departmentNew()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function departmentWithTrashed()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'department_id')->withTrashed();
    }

    public function coefficient()
    {
        return $this->hasOne('App\Http\Models\Coefficient', 'id', 'coefficient_id');
    }
    public function staffCoefficients()
    {
        return $this->hasMany('App\Http\Models\StaffCoefficient', 'staff_id', 'id')
        ->with('coefficient');
    }
    public function staffShift()
    {
        return $this->hasMany('App\Http\Models\StaffShift', 'staff_id', 'id')
        ->with('shift');
    }
}

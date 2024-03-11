<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeStaff extends Model
{
    use SoftDeletes;
    //
    public function tariffScale()
    {
        return $this->hasOne('App\Http\Models\TariffScale', 'id', 'tariff_scale_id');
    }

    public function department()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'department_id');
    }

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    
    public function staff()
    {
        return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id')
        ;
    }
    public function shift()
    {
        return $this->hasOne('App\Http\Models\Shift', 'id', 'shift_id')
        ->select('id','name');
    }

    public function leavingReason()
    {
        return $this->hasOne('App\Http\Models\LeavingReason', 'id', 'leaving_reason_id');
    }

    public function staffLeaving()
    {
        return $this->hasOne('App\Http\Models\StaffLeaving', 'employee_staff_id', 'id');
    }
    // public function ticketUser()
    // {
    //     return $this->belongsTo('App\Http\Models\TicketUser', 'staff_id');
    // }
}

<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffCritical extends Model
{
    use SoftDeletes;

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }

    public function staff()
    {
        return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id');
    }

    public function reserves()
    {
        return $this->hasMany('App\Http\Models\ReservedEmployee', 'critical_staff_id', 'id');
    }
}

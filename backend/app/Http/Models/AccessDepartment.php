<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessDepartment extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    public $fillable = [
        'employee_id',
        'department_id',
        'access_type_id',
    ];

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }

    public function department()
    {
        return $this->hasOne('App\Http\Models\Department', 'id', 'department_id');
    }

    public function accessType()
    {
        return $this->hasOne('App\Http\Models\AccessType', 'id', 'access_type_id');
    }
}

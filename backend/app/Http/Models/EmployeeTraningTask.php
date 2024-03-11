<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTraningTask extends Model
{
    use SoftDeletes;

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    public function tasks()
    {
        return $this->hasMany('App\Http\Models\EmployeeTraningTask', 'employee_id', 'id');
    }
    public function comments()
    {
        return $this->hasMany('App\Http\Models\EmployeeTraningTaskComment', 'employee_traning_task_id', 'id');
    }

    public function file()
    {
        return $this->hasOne('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', 10);
    }
    //
}

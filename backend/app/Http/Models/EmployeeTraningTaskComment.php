<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTraningTaskComment extends Model
{

    public function comments()
    {
        return $this->hasMany('App\Http\Models\EmployeeTraningTaskComment', 'employee_traning_task_id', 'id');
    }

    public function file()
    {
        return $this->hasOne('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', 10);
    }
}

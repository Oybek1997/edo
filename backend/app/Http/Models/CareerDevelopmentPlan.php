<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CareerDevelopmentPlan extends Model
{
    protected $connection= 'workflow_cdpt';
    public $timestamps = true;

    public function departmentType()
    {
        return $this->hasOne('App\Http\Models\CdptDepartmentType', 'id', 'cdpt_department_type_id');
    }

    public function employee()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
}

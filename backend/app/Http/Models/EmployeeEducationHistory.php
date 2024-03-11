<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeEducationHistory extends Model
{
    use SoftDeletes;

    protected $table = 'employee_education_history';

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
    
    public function university()
    {
        return $this->hasOne('App\Http\Models\HrUniversity', 'id', 'university_id');
    }

    public function major()
    {
        return $this->hasOne('App\Http\Models\HrMajor', 'id', 'major_id');
    }

    public function studyType()
    {
        return $this->hasOne('App\Http\Models\HrStudyType', 'id', 'study_type_id');
    }

    public function studyDegree()
    {
        return $this->hasOne('App\Http\Models\HrStudyDegree', 'id', 'study_degree_id');
    }
}

<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DietFood extends Model
{
    protected $connection= 'workflow_medpunkt';
    protected $table = 'diet_foods';

    public function hospitalDiagnosis()
    {
        return $this->hasOne('App\Http\Models\HospitalDiagnosis', 'id', 'hospital_diagnosis_id');
    }

    public function employee()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }

    public function createdBy()
    {
        return $this->setConnection('pgsql')->hasOne('App\User', 'id', 'created_by');
    }
}

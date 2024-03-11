<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalTreatment extends Model
{
    use SoftDeletes;
    protected $connection= 'workflow_medpunkt';

    public function medicineCosts()
    {
        return $this->hasMany('App\Http\Models\MedicineCost', 'medical_treatment_id', 'id');
        // return $this->hasMany('App\Http\Models\MedicineCost', 'id', 'medical_treatment_id');
    }

    public function medicines()
    {
        return $this->hasMany('App\Http\Models\Medicine', 'medical_treatment_id', 'id');
    }

    public function createdBy()
    {
        return $this->setConnection('pgsql')->hasOne('App\User', 'id', 'created_by');
    }
}

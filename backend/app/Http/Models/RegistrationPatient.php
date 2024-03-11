<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationPatient extends Model
{
    use SoftDeletes;

    protected $connection= 'workflow_medpunkt';

    public function hospitalDiagnosis()
    {
        return $this->hasOne('App\Http\Models\HospitalDiagnosis', 'id', 'hospital_diagnosis_id');
    }

    public function employee()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }

    public function ambulanceCalls()
    {
        return $this->hasMany('App\Http\Models\AmbulanceCall', 'registration_patient_id', 'id');
    }

    public function medicalTreatments()
    {
        return $this->hasMany('App\Http\Models\MedicalTreatment', 'registration_patient_id', 'id');
    }

    public function createdBy()
    {
        return $this->setConnection('pgsql')->hasOne('App\User', 'id', 'created_by');
    }
}

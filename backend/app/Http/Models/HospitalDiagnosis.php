<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class HospitalDiagnosis extends Model
{
    use SoftDeletes;
    protected $connection= 'workflow_medpunkt';

    public function diagnosisCode()
    {
        return $this->hasOne('App\Http\Models\DiagnosisCode', 'id', 'diagnosis_code_id');
    }

    public function createdBy()
    {
        return $this->setConnection('pgsql')->hasOne(User::class, 'id', 'created_by');
    }
}

<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosisCode extends Model
{
    protected $connection= 'workflow_medpunkt';
    public $timestamps = false;
}

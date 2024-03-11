<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SpecificSkill extends Model
{
    protected $connection= 'workflow_cdpt';
    public $timestamps = true;

    public function competenceType()
    {
        return $this->hasOne('App\Http\Models\CompetenceType', 'id', 'competence_type_id');
    }

    public function employee()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
}

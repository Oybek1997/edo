<?php

namespace App\Http\Models\Uzautojobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateSubmitted extends Model
{
    protected $connection = 'pg_uzautojobs';
    protected $table = 'candidate_submitted';

    public $timestamps = false;
    // use SoftDeletes;
    public function choiceStatus()
    {
        return $this->hasMany('App\Http\Models\Uzautojobs\SelectionStatus', 'tanlov_id', 'id')
        ->orderBy('id')
        ;
    }
   
}
//workflow_uzautojobs
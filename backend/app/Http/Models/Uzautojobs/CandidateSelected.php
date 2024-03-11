<?php

namespace App\Http\Models\Uzautojobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateSelected extends Model
{
    protected $connection = 'pg_uzautojobs';
    protected $table = 'candidate_selected';

    // protected $fillable = ['address_name',];

    public $timestamps = false;
    // use SoftDeletes;
    
}

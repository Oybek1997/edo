<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmbulanceCall extends Model
{   
    use SoftDeletes;
    protected $connection= 'workflow_medpunkt';
    // public $timestamps = false;
}

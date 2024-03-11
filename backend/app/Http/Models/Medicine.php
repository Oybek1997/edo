<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $connection= 'workflow_medpunkt';
    public $timestamps = false;
}

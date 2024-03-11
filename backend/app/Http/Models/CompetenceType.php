<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetenceType extends Model
{
    protected $connection= 'mysql_workflow_cdpt';
    public $timestamps = false;

}

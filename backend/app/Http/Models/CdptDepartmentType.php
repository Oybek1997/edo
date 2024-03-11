<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class CdptDepartmentType extends Model
{
    protected $connection = 'workflow_cdpt';
    public $timestamps = false;
}

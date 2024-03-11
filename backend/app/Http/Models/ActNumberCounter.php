<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ActNumberCounter extends Model
{
    protected $connection= 'mysql_workflow_org_texnika';
    public $timestamps = false;
}

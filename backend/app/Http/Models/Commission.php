<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $connection= 'mysql_workflow_attestation';
    public $timestamps = false;
}

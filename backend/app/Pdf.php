<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pdf extends Model
{
    public $timestamps = false;
    protected $connection= 'mysql_workflow_log';
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Base64 extends Model
{
    public $timestamps = false;
    protected $connection= 'mysql_workflow_log';
    protected $table = 'base64';
}

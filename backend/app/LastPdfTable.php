<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LastPdfTable extends Model
{
    //
    public $timestamps = false;
    protected $connection= 'workflow_log';
}

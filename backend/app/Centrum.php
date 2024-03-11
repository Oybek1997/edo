<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Centrum extends Model
{
    protected $connection = 'workflow_log';
    public $timestamps = false;
    //
    protected $fillable = [
    ];
}

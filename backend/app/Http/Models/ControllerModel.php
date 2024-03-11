<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControllerModel extends Model
{

    protected $connection = 'user_logs';
    protected $table = 'controllers';
    // public $timestamps = false;
    //use SoftDeletes;

}

<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{

    protected $connection = 'user_logs';
    protected $table = 'actions';
    // public $timestamps = false;
    //use SoftDeletes;

}

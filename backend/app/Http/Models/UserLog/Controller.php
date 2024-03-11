<?php

namespace App\Http\Models\UserLog;

use Illuminate\Database\Eloquent\Model;

class Controller extends Model
{
    protected $connection = 'user_logs';
    protected $table = 'controllers';
    public $timestamps = false;
    protected $fillable = ['name'];

}

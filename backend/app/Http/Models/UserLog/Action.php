<?php

namespace App\Http\Models\UserLog;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $connection = 'user_logs';
    protected $table = 'actions';
    public $timestamps = false;
    protected $fillable = ['name'];
}

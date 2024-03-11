<?php

namespace App\Http\Models\UserLog;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'user_logs';
    protected $table = 'logs';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address_id',
        'controller_id',
        'action_id',
        'created_at'
    ];
}

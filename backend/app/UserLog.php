<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $connection= 'workflow_log';
    // protected $table = 'zarlib.z08ptpf';
    public $timestamps = false;
    public $fillable = [
        'user_id',
        'operation',
        'controller',
        'action',
        'object_id',
        'user_ip',
        'user_browser',
        'user_agent',
        'user_browser_version',
        'user_platform',
        'user_device_type',
        'created_at',
        'request_json',
        'url',
    ];
}

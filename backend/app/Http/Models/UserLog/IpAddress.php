<?php

namespace App\Http\Models\UserLog;

use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    protected $connection = 'user_logs';
    protected $table = 'ip_addresses';
    public $timestamps = false;
    protected $fillable = ['name'];

}
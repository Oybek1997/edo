<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpAdress extends Model
{

    protected $connection = 'user_logs';
    protected $table = 'ip_addresses';
    // public $timestamps = false;
    //use SoftDeletes;

}

<?php

namespace App\Http\Models;

use App\User;
use App\Http\Models\IpAdress;
use App\Http\Models\Controller;
use App\Http\Models\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLog extends Model
{

    protected $connection = 'user_logs';
    protected $table = 'logs';
    // public $timestamps = false;
    //use SoftDeletes;


    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
    public function ipadress()
    {
        return $this->hasOne(IpAdress::class,'id','ip_address_id');
    }
    public function controller()
    {
        return $this->hasOne(ControllerModel::class,'id','controller_id');
    }
    public function action()
    {
        return $this->hasOne(Action::class,'id','action_id');
    }
}

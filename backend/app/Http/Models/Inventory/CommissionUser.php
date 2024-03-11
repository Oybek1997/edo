<?php

namespace App\Http\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionUser extends Model
{
    protected $connection = 'pg_inventory';
    protected $table = 'commission_user';
    // public $timestamps = false;
    // use SoftDeletes;
   
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
    public function PermissionsUsers()
    {
        return $this->hasMany('App\Http\Models\Inventory\PermissionsUsers', 'user_id', 'id')->select('id','user_id','permissions_id');
    }
   
}

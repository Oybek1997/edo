<?php

namespace App\Http\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionsUsers extends Model
{
    protected $connection = 'pg_inventory';
    protected $table = 'permissionsusers';
    // public $timestamps = false;
    // use SoftDeletes;
   
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
    public function userPerr()
    {
        return $this->hasOne('App\Http\Models\Inventory\Permissions', 'id', 'permissions_id')->select('id','name','description');
    }
   
}

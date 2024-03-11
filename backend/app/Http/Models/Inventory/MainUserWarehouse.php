<?php

namespace App\Http\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainUserWarehouse extends Model
{
    protected $connection = 'pg_inventory_main';
    protected $table = 'main_user_warehouses';
    // public $timestamps = false;
    use SoftDeletes;
    
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}

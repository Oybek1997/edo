<?php

namespace App\Http\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainWarehouse extends Model
{
    protected $connection = 'pg_inventory_main';
    protected $table = 'main_warehouses';
    use SoftDeletes;
    
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }

    public function mainProducts()
    {
        return $this->hasMany(MainProduct::class, 'warehouse_id', 'id');
    }

    public function mainUserWarehouse()
    {
        return $this->hasOne(MainUserWarehouse::class, 'warehouse_id', 'id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryUniqueProduct extends Model
{
    protected $connection= 'workflow_log';
    public $timestamps = false;

    public function warehouse()
    {
        return $this->hasOne('App\InventoryWarehouseList', 'id', 'warehouse_id');
    }

    public function address()
    {
        return $this->hasOne('App\InventoryAddress', 'id', 'address_id');
    }

    public function inventoryBlankLists()
    {
        return $this->hasMany('App\InventoryBlankList', 'part_number', 'part_number')->select('part_number','real_stock')->where('year',2022);
    }
}

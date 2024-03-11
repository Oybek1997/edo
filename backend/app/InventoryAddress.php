<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryAddress extends Model
{
    protected $connection= 'workflow_log';
    public $timestamps = false;

    public function warehouse()
    {
        return $this->hasOne('App\InventoryWarehouseList', 'id', 'warehouse_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryProductList extends Model
{
    protected $connection= 'mysql_workflow_log';
    public $timestamps = false;

    public function warehouse()
    {
        return $this->hasOne('App\InventoryWarehouseList', 'id', 'warehouse_id');
    }
}

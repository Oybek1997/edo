<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryWarehouseList extends Model
{
    public $timestamps = false;
    protected $connection= 'workflow_log';
}

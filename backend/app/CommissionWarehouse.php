<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommissionWarehouse extends Model
{
    public $timestamps = false;
    protected $connection= 'mysql_workflow_log';
    protected $table = 'commission_warehouse';
}

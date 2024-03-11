<?php

namespace App\Http\Models\SapIntegration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseResponsible extends Model
{
    // public $timestamps = false;
    use SoftDeletes;
    protected $connection= 'workflow_sap';
    // public $table="workflow_sap.nelekvids";
    // protected $table = 'nelekvids';
    public function employeeResponsible()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'responsible_employee_id');
    }
    public function accountantResponsible()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'responsible_accountant_id');
    }
    public function warehouse()
    {
        return $this->hasMany('App\Http\Models\SapIntegration\Warehouse', 'id', 'warehouse_id');
    }
}

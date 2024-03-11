<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryCommission extends Model
{
    protected $connection= 'workflow_log';
    public $timestamps = false;

    public function warehouses()
    {
        return $this->belongsToMany('App\InventoryWarehouseList', 'commission_warehouse', 'commission_id', 'warehouse_id');
    }

    public function responsible()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'responsible_person');
    }

    public function chairman()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'chairman');
    }

    public function member1()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'member1');
    }

    public function member2()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'member2');
    }

    public function member3()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'member3');
    }

    public function member4()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'member4');
    }

    public function member5()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'member5');
    }

    public function shtab_member1()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'shtab_member1');
    }

    public function shtab_member2()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'shtab_member2');
    }

    public function control_group()
    {
        return $this->setConnection('pgsql')->hasOne('App\Http\Models\Employee', 'id', 'control_group');
    }
}

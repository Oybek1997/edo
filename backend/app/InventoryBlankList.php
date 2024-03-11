<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryBlankList extends Model
{
    protected $connection= 'workflow_log';
    protected $appends = ['created_employee','checked_employee'];

    public function warehouse()
    {
        return $this->hasOne('App\InventoryWarehouseList', 'id', 'warehouse_id');
    }

    public function product()
    {
        return $this->hasOne('App\InventoryUniqueProduct', 'part_number', 'part_number');
    }

    public function commission()
    {
        return $this->hasOne('App\InventoryCommission', 'id', 'inventory_commission_id');
    }

    public function address()
    {
        return $this->hasOne('App\InventoryAddress', 'id', 'inventory_address_id');
    }

    public function getCreatedEmployeeAttribute()
    {
        $user = User::find($this->created_by);
        if ($user && $user->employee) {
            $employee = $user->employee;
            return substr($employee->firstname_uz_latin, 0, 1).'. '.substr($employee->middlename_uz_latin, 0, 1).'. '.$employee->lastname_uz_latin;
        }
        return null;
    }

    public function getCheckedEmployeeAttribute()
    {
        $user = User::find($this->checked_by);
        if ($user && $user->employee) {
            $employee = $user->employee;
            return substr($employee->firstname_uz_latin, 0, 1).'. '.substr($employee->middlename_uz_latin, 0, 1).'. '.$employee->lastname_uz_latin;
        }
        return null;
    }

    // public function createdBy()
    // {
    //     return $this->hasOne('App\User', 'id', 'created_by');
    // }

    // public function checkedBy()
    // {
    //     return $this->hasOne('App\User', 'id', 'checked_by');
    // }
}

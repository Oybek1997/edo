<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CarPurchase extends Model
{
    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id')->where('is_active', 1);
    }

    public function carModel()
    {
        return $this->hasOne('App\Http\Models\CarModel', 'id', 'car_model_id');
    }

    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function accountantEmployee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'account_employee_id');
    }

    public function file()
    {
        return $this->hasOne('App\Http\Models\File', 'object_id', 'id')->where('object_type_id', 7);
    }
}

<?php

namespace App\Http\Models\ClothingOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeClothe extends Model
{
    protected $connection = 'clothing_order';
    protected $table = 'employee_clothes';
    // public $timestamps = false;
    use SoftDeletes;

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id')->where('is_active', 1);
    }

    public function products()
    {
        return $this->hasOne('App\Http\Models\ClothingOrder\Product', 'id', 'product_id');
    }
    public function sizes()
    {
        return $this->hasOne('App\Http\Models\ClothingOrder\Size', 'id', 'size_id');
    }

    public function createdBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }

}
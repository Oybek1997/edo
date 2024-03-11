<?php

namespace App\Http\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    protected $connection = 'pg_inventory';
    protected $table = 'items';
    // public $timestamps = false;
    use SoftDeletes;
    // public function createdBy()
    // {
    //     return $this->hasOne(\App\User::class, 'id', 'created_by');
    // }

    // public function updatedBy()
    // {
    //     return $this->hasOne(\App\User::class, 'id', 'updated_by');
    // }
    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d H:i',
    //     'updated_at' => 'datetime:Y-m-d H:i',
    //     'deleted_at' => 'datetime:Y-m-d H:i',
    // ];
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
    public function address()
    {
        return $this->hasOne('App\Http\Models\Inventory\Address', 'id', 'address_id')
        ->with('warehouse')
        ->select('address_name','warehouse_id','id');
    }
    public function quarter()
    {
        return $this->hasOne('App\Http\Models\Inventory\Quarter', 'id', 'quarter_id')
        ->select('quarter','status','year','id');
    }
    public function eo()
    {
        return $this->hasOne('App\Http\Models\Inventory\Eo', 'id', 'eo_id')
        ->select('eo_number','id');
    }
    public function product()
    {
        return $this->hasOne('App\Http\Models\Inventory\Product', 'id', 'product_id');
    }
    public function warehouseId()
    {
        return $this->hasOne('App\Http\Models\Inventory\Warehouse', 'id', 'product_id');
    }
    public function updatedBy()
    {
        return $this->hasOne('App\Http\Models\Inventory\CommissionUser', 'id', 'updated_by');
        // return $this->hasOne('App\User', 'id', 'updated_by')
        // ->select('eimzo_name as name','email','id');
    }
    
    public function users()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
    public function createdBy()
    {
        return $this->hasOne('App\Http\Models\Inventory\CommissionUser', 'id', 'created_by');
        // return $this->hasOne('App\User', 'id', 'created_by')
        // ->select('employee_id','email','id')
        // ->with('employee:id,firstname_uz_latin,lastname_uz_latin,middlename_uz_latin,tabel')
        // ;
    }

    // public function createdBy()
    // {
    //     return $this->hasOne(\APP\User::class, 'id', 'created_by');
    // }
}

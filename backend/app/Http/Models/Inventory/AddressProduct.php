<?php

namespace App\Http\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddressProduct extends Model
{
    protected $connection = 'pg_inventory';
    protected $table = 'address_products';

    protected $fillable = ['address_id', 'product_id', 'stock'];

    // public $timestamps = false;
    // use SoftDeletes;
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
        return $this->hasOne('App\Http\Models\Inventory\Address', 'id', 'address_id');
    }
    public function product()
    {
        return $this->hasOne('App\Http\Models\Inventory\Product', 'id', 'product_id');
    }
}

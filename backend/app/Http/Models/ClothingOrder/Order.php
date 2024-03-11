<?php

namespace App\Http\Models\ClothingOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    protected $connection = 'clothing_order';
    protected $table = 'public.orders';
    // public $timestamps = false;
    use SoftDeletes;


    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id')->orderBy('order_details.id');
    }
    public function createdBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }
    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}

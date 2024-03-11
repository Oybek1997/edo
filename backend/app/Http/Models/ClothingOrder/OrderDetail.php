<?php

namespace App\Http\Models\ClothingOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    protected $connection = 'clothing_order';
    protected $table = 'public.order_details';
    // public $timestamps = false;
    use SoftDeletes;

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function employee()
    {
        return $this->hasOne(\App\Http\Models\Employee::class, 'id', 'employee_id');
    }

    public function product()
    {
        return $this->hasOne(\App\Http\Models\ClothingOrder\Product::class, 'id', 'product_id');
    }

    public function size()
    {
        return $this->hasOne(\App\Http\Models\ClothingOrder\Size::class, 'id', 'size_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}

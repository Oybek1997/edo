<?php

namespace App\Http\Models\ClothingOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    protected $connection = 'clothing_order';
    protected $table = 'products';
    // public $timestamps = false;
    use SoftDeletes;

    public function sizes()
    {
        return $this->hasMany(Size::class, 'product_id', 'id')->orderBy('sizes.size', 'asc');
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

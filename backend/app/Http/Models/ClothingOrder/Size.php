<?php

namespace App\Http\Models\ClothingOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    protected $connection = 'clothing_order';
    protected $table = 'sizes';
    public $timestamps = false;
    use SoftDeletes;

}

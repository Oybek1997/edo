<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'shop';

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shopid', 'id');
    }

    public function linestop()
    {
        return $this->hasMany(Plcdata::class, 'lineid', 'id');
    }
    
}

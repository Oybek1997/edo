<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'provider';
    
}

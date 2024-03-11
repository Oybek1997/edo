<?php

namespace App\Http\Models\Linestop;

use Illuminate\Database\Eloquent\Model;

class Productmodel extends Model
{
    protected $connection = 'linestop';
    public $timestamps = false;
    public $table = 'productmodel';
    protected $fillable = ['name', 'sap_code', 'region', 'address'];
    
}

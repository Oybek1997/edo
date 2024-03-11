<?php

namespace App\Http\Models\Supplytransport;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'pg_supply_transport';
    public $timestamps = false;
    public $table = 'department';
    protected $fillable = ['id', 'name'];

}

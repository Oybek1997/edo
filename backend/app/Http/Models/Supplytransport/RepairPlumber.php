<?php

namespace App\Http\Models\Supplytransport;

use Illuminate\Database\Eloquent\Model;

class RepairPlumber extends Model
{
    protected $connection = 'pg_supply_transport';
    public $timestamps = false;
    public $table = 'repair_plumber';
    protected $fillable = ['id', 'table_number', 'dep_code', 'surname', 'name', 'description', 'status'];
    
}

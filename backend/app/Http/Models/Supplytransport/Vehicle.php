<?php

namespace App\Http\Models\Supplytransport;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $connection = 'pg_supply_transport';
    public $timestamps = false;
    public $table = 'vehicle';
    protected $fillable = ['id', 'transport_id', 'exploitation_date', 'inventory_number', 'belong_dep', 'dep_shop', 'description'];

    public function transport_type()
    {
        return $this->hasOne('App\Http\Models\Supplytransport\TransportType', 'id', 'transport_id');
    }    
}

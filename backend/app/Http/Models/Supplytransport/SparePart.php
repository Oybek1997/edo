<?php

namespace App\Http\Models\Supplytransport;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $connection = 'pg_supply_transport';
    public $timestamps = false;
    public $table = 'spare_part';
    protected $fillable = ['id', 'transport_id', 'sap_number', 'name', 'standard_indicator', 'estimeted_month', 'description'];

    public function transport_type()
    {
        return $this->hasOne('App\Http\Models\Supplytransport\TransportType', 'id', 'transport_id');
    }    
}

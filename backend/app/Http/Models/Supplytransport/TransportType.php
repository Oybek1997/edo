<?php

namespace App\Http\Models\Supplytransport;

use Illuminate\Database\Eloquent\Model;

class TransportType extends Model
{
    protected $connection = 'pg_supply_transport';
    public $timestamps = false;
    public $table = 'transport_type';
    protected $fillable = ['id', 'name'];


    public function sparepart()
    {
        return $this->belongsTo('App\Http\Models\Supplytransport\SparePart', 'transport_id', 'id');
    }
    
}

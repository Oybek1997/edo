<?php

namespace App\Http\Models\EDI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asn extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.asns';
    // public $timestamps = false;
    use SoftDeletes;


    public function shipmentType()
    {
        return $this->hasOne(ShipmentType::class, 'id', 'shipment_type_id');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class, 'id', 'contract_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function asnDetails()
    {
        return $this->hasMany(AsnDetail::class, 'asn_id', 'id');
    }

    public function createdBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d H:i',
    //     'updated_at' => 'datetime:Y-m-d H:i',
    //     'deleted_at' => 'datetime:Y-m-d H:i',
    // ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}
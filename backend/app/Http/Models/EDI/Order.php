<?php

namespace App\Http\Models\EDI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.orders';
    // public $timestamps = false;
    use SoftDeletes;


    public static function reduceShipmentQuantity($id)
    {
        try {

            $order = Self::find($id);
            if ($order) {
                $shipment_quantity = 0;
                $shipment_price = 0;
                foreach ($order->asnDetails as $key => $value) {
                    $shipment_quantity += $value->shipment_quantity;
                    $shipment_price += $value->shipment_quantity * $value->orderDetail->contractDetail->price;
                }
                $order->shipment_quantity = $shipment_quantity;
                $order->shipment_price = $shipment_price;
                $order->save();
            }
        } catch (\Throwable $th) {
            return 0;
            // throw $th;
        }
    }

    public static function reduceOrderQuantity($id)
    {
        try {
            $order = Self::find($id);
            if ($order) {
                $order_quantity = 0;
                $order_price = 0;
                foreach ($order->orderDetails as $key => $value) {
                    $order_quantity += $value->order_quantity;
                    $order_price += $value->order_quantity * $value->contractDetail->price;
                }
                $order->order_quantity = $order_quantity;
                $order->total_price = $order_price;
                $order->save();
            }
        } catch (\Throwable $th) {
            return 0;
            //throw $th;
        }
    }

    public function getOrderQuantityAttribute()
    {
        $detail_quantity_sum = 0;
        foreach ($this->orderDetails as $key => $value) {
            $detail_quantity_sum += $value->order_quantity;
        }
        return $detail_quantity_sum;
    }

    public function getOrderPriceAttribute()
    {
        $detail_price = 0;
        foreach ($this->orderDetails as $key => $value) {
            $detail_price += $value->price;
        }
        return $detail_price;
    }

    public function asns()
    {
        return $this->hasMany(Asn::class, 'order_id', 'id');
    }
    public function asnDetails()
    {
        return $this->hasManyThrough(
            AsnDetail::class,
            Asn::class,
            'order_id', // Foreign key on users table...
            'asn_id', // Foreign key on posts table...
            'id', // Local key on countries table...
            'id' // Local key on users table...
        );
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
    public function contract()
    {
        return $this->hasOne(Contract::class, 'id', 'contract_id');
    }
    public function createdBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }
    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}

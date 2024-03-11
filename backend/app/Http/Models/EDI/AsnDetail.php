<?php

namespace App\Http\Models\EDI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsnDetail extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.asn_details';
    // public $timestamps = false;
    use SoftDeletes;


    public function asn()
    {
        return $this->hasOne(Asn::class, 'id', 'asn_id');
    }

    public function orderDetail()
    {
        return $this->hasOne(OrderDetail::class, 'id', 'order_detail_id');
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

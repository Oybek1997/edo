<?php

namespace App\Http\Models\EDI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.order_details';
    // public $timestamps = false;
    use SoftDeletes;

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function contractDetail()
    {
        return $this->hasOne(ContractDetail::class, 'id', 'contract_detail_id');
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

<?php

namespace App\Http\Models\EDI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractDetail extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.contract_details';
    // public $timestamps = false;
    public function contract()
    {
        return $this->hasOne(Contract::class, 'id', 'contract_id');
    }
    public function material()
    {
        return $this->hasOne(Material::class, 'id', 'material_id');
    }
    public function targetWarehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'target_warehouse_id');
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone('Asia/Tashkent')->format('Y-m-d H:i:s');
    }
}

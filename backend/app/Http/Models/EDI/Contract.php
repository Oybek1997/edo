<?php

namespace App\Http\Models\EDI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.contracts';
    use SoftDeletes;

    public $appends = ['status'];

    public function getStatusAttribute()
    {
        return $this->active_from <= date('Y-m-d') && $this->active_to >= date('Y-m-d');
    }

    public function businessPartner()
    {
        return $this->hasOne(BusinessPartner::class, 'id', 'bp_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function contractDetails()
    {
        return $this->hasMany(ContractDetail::class, 'contract_id', 'id')->orderBy('position');
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

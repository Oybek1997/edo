<?php

namespace App\Http\Models\EDI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;


class UserBusinessPartner extends Model
{
    protected $connection = 'edi';
    protected $table = 'edi.user_business_partner';
    // public $timestamps = false;
    use SoftDeletes;
    

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function business_partner()
    {
        return $this->hasOne(BusinessPartner::class, 'id', 'bp_id');
    }
   

    public function createdBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
        'deleted_at' => 'datetime:Y-m-d H:i',
    ];
}

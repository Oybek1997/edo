<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAddress extends Model
{
    use SoftDeletes;
    //

    public function addressType()
    {
        return $this->hasOne('App\Http\Models\AddressType', 'id', 'address_type_id');
    }

    public function country()
    {
        return $this->hasOne('App\Http\Models\Country', 'id', 'country_id');
    }

    public function region()
    {
        return $this->hasOne('App\Http\Models\Region', 'id', 'region_id');
    }

    public function district()
    {
        return $this->hasOne('App\Http\Models\District', 'id', 'district_id');
    }
}

<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;

    public function country()
    {
        return $this->hasOne('App\Http\Models\Country', 'id', 'country_id');
    }



}

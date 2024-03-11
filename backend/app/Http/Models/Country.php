<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;
    //
    public function regions()
    {
        return $this->hasMany('App\Http\Models\Region', 'country_id', 'id');
    }
}

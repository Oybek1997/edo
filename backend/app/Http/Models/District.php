<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use SoftDeletes;
    public function region()
    {
        return $this->hasOne('App\Http\Models\Region', 'id', 'region_id');
    }
}

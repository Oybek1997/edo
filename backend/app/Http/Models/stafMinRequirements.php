<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class stafMinRequirements extends Model
{
    // use SoftDeletes;
    //
    public $incrementing = false;
    public $timestamps = false;

    public function minReqType()
    {
        return $this->hasOne('App\Http\Models\minRequirements', 'id', 'min_req_id');
    }
}

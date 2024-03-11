<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;
    //
    public function company()
    {
        return $this->hasOne('App\Http\Models\Company', 'id', 'company_id');
    }

    public function positionType()
    {
	    return $this->hasOne('App\Http\Models\PositionType', 'id', 'position_type_id');

    }
    
    public function staff()
    {
        return $this->hasMany('App\Http\Models\Staff', 'position_id', 'id')->where('is_active',1);
    }
}

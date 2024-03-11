<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class StaffFile extends Model
{
    public function staff()
    {
	    return $this->hasOne('App\Http\models\Staff', 'id', 'staff_id');
    }
    public function files()
    {
        return $this->hasMany('App\Http\Models\File', 'file_id', 'id');
    }
    public function objectType()
    {
        return $this->hasMany('App\Http\Models\ObjectType', 'object_type_id', 'id');
    }
}

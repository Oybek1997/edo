<?php

namespace App\Http\Models;;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplaensRelative extends Model
{
    // use SoftDeletes;
    //
    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'relative_id');
    }
    public function relative()
    {
        return $this->hasOne('App\Http\Models\FamilyRelative', 'id', 'relative_type_id');
    }
}

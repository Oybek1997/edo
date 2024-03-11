<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class StaffRequirement extends Model
{
    //

    public function staff()
    {
        return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id');
    }

    public function requirement()
    {
        return $this->hasOne('App\Http\Models\Requirements', 'id', 'requirement_id');
    }
}

<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class StaffShift extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    public function shift()
    {
        return $this->hasOne('App\Http\Models\Shift', 'id', 'shift_id');
    }

}
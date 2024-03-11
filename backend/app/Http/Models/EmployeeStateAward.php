<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeStateAward extends Model
{
    use SoftDeletes;
    //
    public function hrStateAward()
    {
        return $this->hasOne('App\Http\Models\HrStateAward', 'id', 'hr_state_award_id');
    }
}

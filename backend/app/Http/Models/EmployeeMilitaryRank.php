<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeMilitaryRank extends Model
{
    use SoftDeletes;
    //

    public function hrMilitaryRank()
    {
        return $this->hasOne('App\Http\Models\HrMilitaryRank', 'id', 'hr_military_rank_id');
    }
}

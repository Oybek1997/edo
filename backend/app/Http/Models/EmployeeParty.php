<?php

namespace App\Http\Models;;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeParty extends Model
{
    use SoftDeletes;
    //
    public function hrParty()
    {
        return $this->hasOne('App\Http\Models\HrParty', 'id', 'hr_party_id');
    }
}

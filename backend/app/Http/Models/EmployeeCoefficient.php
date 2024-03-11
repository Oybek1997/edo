<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeCoefficient extends Model
{
    use SoftDeletes;
    //
    public function coefficient()
    {
        return $this->hasOne('App\Http\Models\Coefficient', 'id', 'coefficient_id');
    }
}

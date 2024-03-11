<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffCoefficient extends Model
{
    protected $connection = 'pgsql';
    use SoftDeletes;
    public function staff()
    {
        return $this->hasOne('App\Http\Models\Staff', 'id', 'staff_id');
    }
    public function coefficient()
    {
        return $this->hasOne('App\Http\Models\Coefficient', 'id', 'coefficient_id');
    }
}

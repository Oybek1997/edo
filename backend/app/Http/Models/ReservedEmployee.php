<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedEmployee extends Model
{
    protected $table = "reserved_employees";

    public $timestamps = false;

    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
}

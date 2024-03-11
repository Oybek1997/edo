<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Mailing extends Model
{
    public function employee()
    {
        return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
    }
}

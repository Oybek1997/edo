<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class KpiFactComission extends Model
{
    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}

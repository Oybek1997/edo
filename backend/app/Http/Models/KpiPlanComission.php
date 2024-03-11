<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class KpiPlanComission extends Model
{
    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
    public function documentDetail()
    {
        return $this->hasOne(DocumentDetail::class, 'id', 'd_d_id');
    }
}

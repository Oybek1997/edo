<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentDetailEmployee extends Model
{
    use SoftDeletes;
    //
    public function Employee()
	{
		return $this->hasOne('App\Http\Models\Employee', 'id', 'employee_id');
	}

    public function tariffScale()
    {
        return $this->hasOne(TariffScale::class, 'id', 'tariff_scale_id');
    }

    public function range()
    {
        return $this->hasOne(Range::class, 'id', 'range_id');
    }

    public function otgulDates()
    {
        return $this->hasMany(OtgulDate::class, 'document_detail_employee_id', 'id');
    }
}